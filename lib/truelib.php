<?php declare(strict_types = 1);

use dqdp\SQL\Select;
use dqdp\Template;
use dqdp\TemplateBlock;
use dqdp\TODO;

function tm_shutdown()
{
	global $i_am_admin;

	if($i_am_admin)
	{
		$is_html = 0;
		$headers = headers_list();
		foreach($headers as $h)
		{
			if(stripos(strtolower($h), "content-type: text/html") === 0)
			{
				$is_html = 1;
				break;
			}
		}

		if($is_html)
		{
			print '<link rel=stylesheet href="/css/highlight/vs.min.css">';
			print '<script src="/js/highlight.min.js"></script>';
			print '<script>hljs.highlightAll();</script>';
		}
	}
}

function pw_validate(string $p1, string $p2, array &$error_msg): bool {
	if($p1 != $p2){
		$error_msg[] = 'Paroles nesakrīt!';
		return false;
	}

	$resut = PwValidator::validate($p1);

	if(PwValidator::valid_pass($resut)){
		return true;
	}

	if(!$resut->HAS_LEN)        $error_msg[] = 'Parole par īsu';
	if(!$resut->HAS_ALPHA)      $error_msg[] = 'Parolē nav standarta burtu';
	if(!$resut->HAS_NON_ALPHA)  $error_msg[] = 'Parolē nav simbolu vai ciparu';
	if(!$resut->HAS_NO_REPEATS) $error_msg[] = 'Parolē ir sacīgi simboli';

	return false;
}

function new_template(string $file_name): ?Template {
	global $sys_template_root;

	return new Template($sys_template_root.DIRECTORY_SEPARATOR.$file_name);
}

function forum_add_theme(MainModule $template, Template $T, array $forum_data, array $post_data): bool
{
	global $ip;

	if(!User::logged())
	{
		$template->not_logged();
		return false;
	}

	if(User::blacklisted())
	{
		$error_msg[] = "Blacklisted IP: $ip";
		return false;
	}

	$error_msg = $error_fields = [];
	if(empty($post_data['forum_name']))
	{
		$error_msg[] = "Nav norādīts tēmas nosaukums";
		$error_fields[] = 'forum_name';
	}

	if(empty($post_data['forum_data']))
	{
		$error_msg[] = "Nav norādīts ziņojums";
		$error_fields[] = 'forum_data';
	}

	# Tirgus
	if($forum_data['forum_id'] == 107488){
		$entered_days = time() - strtotime(User::get_val('l_entered')) / (3600 * 24);
		if(($entered_days < 10) || (User::get_val('votes_plus') - User::get_val('votes_minus') < 10)){
			$error_msg[] = 'Nepietiekams reitings. Jābūt vismaz 10 dienu vecam vai (plusi - mīnusi) vismaz 10';
		}
	}

	if($error_msg){
		$T->enable('BLOCK_forum_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	set_error_fields($T, $error_fields);

	$T->set_array(specialchars($post_data), 'BLOCK_loggedin');

	if($error_msg){
		return false;
	}

	$R = prepare_res(
		res_resid: $forum_data['res_id'],
		table_id: Table::FORUM,
		res_name: $post_data['forum_name'],
		res_data: $post_data['forum_data'],
	);

	$forum_id = DB::withNewTrans(function() use ($R){
		if($res_id = $R->insert()){
			return ForumType::initFrom(new ForumDummy(
				res_id: $res_id,
				forum_allow_childs: 0
			))->insert();
		}
	});

	new TODO("redirect");

	return (bool)$forum_id;
}

function forum_themes(
	MainModule $template,
	array $forum_data,
	string $action,
	int $fpp,
	int $page_id,
	int $pages_visible_to_sides,
): ?Template
{
	if(User::logged())
	{
		Forum::markThemeCount($forum_data);
	}

	$T = $template->add_file('forum/theme.tpl');
	$T->set_array($forum_data);

	$T->set_var('current_theme_name', specialchars($forum_data['res_name']));
	$T->set_var('current_theme_route', Forum::RouteFromRes($forum_data));

	if($forum_data['forum_id'] == 107488){
		$T->enable('BLOCK_forumdata_bazar');
	}

	// $forum = new Forum;

	if($action == 'add_theme')
	{
		if(forum_add_theme($template, $T, $forum_data, post('data')))
		{
			return null;
		}
	} else {
		# TODO: kaut kā stulbi. Name collision
		$T->set_var('forum_name', '', 'BLOCK_loggedin');
		$T->set_var('forum_data', '', 'BLOCK_loggedin');
	}

	if(User::logged())
	{
		$T->enable('BLOCK_loggedin');
	} else {
		$T->enable('BLOCK_notloggedin');
	}

	$params = [
		"res_resid"=>$forum_data['res_id'],
		"page"=>$page_id,
		"items_per_page"=>$fpp,
	];

	if(User::get_val('l_forumsort_themes') == Forum::SORT_LASTCOMMENT)
	{
		$params["order"] = "COALESCE(res_comment_last_date, res_entered) DESC";

		$T->enable('BLOCK_info_sort_C');
	} else {
		$params["order"] = "res_entered DESC";
		$T->enable('BLOCK_info_sort_T');
	}

	$items = Forum::load($params);

	if($items)
	{
		$T->enable('BLOCK_forum_themes');
	} else {
		$T->enable('BLOCK_noforum');
	}

	$BLOCK_forum = $T->get_block('BLOCK_forum');
	foreach($items as $item)
	{
		$BLOCK_forum->enable_if(Res::hasNewComments($item), 'BLOCK_comments_new');

		$BLOCK_forum->set_array(specialchars($item));
		$BLOCK_forum->set_var('res_route', Forum::RouteFromRes($item));
		$BLOCK_forum->set_var('res_date', proc_date($item['res_entered']));
		$BLOCK_forum->parse(TMPL_APPEND);
	}

	$forum_count = (int)$forum_data['res_child_count'];
	forum_pages($page_id, $forum_count, $fpp, $pages_visible_to_sides, $T);

	// $forum->set_forum_path($T, $forum_id);

	return $T;
}

function forum_pages(
	int $page_id,
	int $forum_count,
	int $fpp,
	int $pages_visible_to_sides,
	Template $T,
)
{
	$total_pages = ceil($forum_count / $fpp);

	# uzstaadam pages
	if($total_pages > 1)
	{
		$_pvs = $pages_visible_to_sides;
		$sep_count = 0;
		$visible_pages = $_pvs + (
			$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : $_pvs - ($total_pages - $page_id))
			: $_pvs - $page_id + 1
		);

		$side_sep = 1 + (
			$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : 1)
			: 1
		);

		$T->enable('BLOCK_is_pages');
		$BLOCK_page = $T->get_block('BLOCK_page');
		for($p = 1; $p <= $total_pages; $p++)
		{
			$p_id = ($total_pages > 10) && ($p < 10) ? "0$p" : $p;
			$BLOCK_page->set_var('p_id', $p_id);
			$BLOCK_page->set_var('page_id', $p);

			// if($p_id > 1){
			// 	$ps = $BLOCK_page->get_block('BLOCK_page_switcher');
			// 	printr($ps->dump());
			// 	printr($ps->get_var('p_id'));
			// 	// printr($BLOCK_page->get_block('BLOCK_page_switcher'));
			// 	die;
			// }

			# atziimee, tekoshu page
			if($p == $page_id)
			{
				$BLOCK_page->set_var('page_style', ' style="color: #00AC00;"');
			} else {
				$BLOCK_page->set_var('page_style', '');
			}

			# skippo pa nevajadziigaas pages
			if(abs($p - $page_id) > $visible_pages)
			{
				$sep_count++;
				$BLOCK_page->set_var('page_seperator', (abs($p - $page_id) > $visible_pages) && (abs($p - $page_id) - $visible_pages <= $side_sep) ? '[..]' : '');
				$BLOCK_page->disable('BLOCK_page_switcher');
			} else {
				$BLOCK_page->enable('BLOCK_page_switcher');
				$BLOCK_page->set_var('page_seperator', '');
			}

			$BLOCK_page->parse(TMPL_APPEND);
		}
	}

	# prev
	$T->set_var('prev_page_id', ($page_id > 1) ? $page_id - 1 : $page_id);

	# next
	$T->set_var('next_page_id', ($page_id < $total_pages) ? $page_id + 1 : $page_id);
}

function forum_det(
	MainModule $template,
	array $forum_data,
	string $action,
	string $hl,
): ?Template
{
	$res_id = (int)$forum_data['res_id']??0;

	// $forum = new Forum;

	$T = $template->add_file('forum/det.tpl');
	// $T->set_var('res_name', specialchars($forum_data['res_name']));

	$T->set_array($forum_data);
	$T->set_var('res_date', proc_date($forum_data['res_entered']));
	$T->set_var('res_votes', format_vote($forum_data['res_votes']));
	$T->set_var('comment_vote_class', comment_vote_class($forum_data['res_votes']));

	if(User::logged()){
		$T->enable('BLOCK_vote_control');
	}

	if(User::logged() && $forum_data['l_hash']){
		$T->enable('BLOCK_profile_link');
	} else {
		$T->disable('BLOCK_profile_link');
	}

	$C = $template->add_file('comments.tpl');

	# Comments
	$params['order'] = User::get_val('l_forumsort_msg') == Forum::SORT_DESC
		? "res_entered DESC"
		: "res_entered"
	;

	$T->enable(User::get_val('l_forumsort_msg') == Forum::SORT_DESC ? 'BLOCK_info_sort_D': 'BLOCK_info_sort_A');

	$comments = get_res_comments($res_id, $params);
	// printr($forum_data, $params, $comments);
	// die;

	# XXX : hack, vajag rādīt pa taisno foruma ierakstu
	// if(($forum_data['forum_display'] == Forum::DISPLAY_DATA) && !empty($comments[0]))
	// {
	// 	# Ja sakārtots dilstoši, tad jāaiztiek ir pēdējais komments
	// 	if(User::get_val('l_forumsort_msg') == Forum::SORT_DESC)
	// 	{
	// 		array_unshift($comments, array_pop($comments));
	// 	}
	// 	$comments[0]['res_data_compiled'] = $forum_data['res_data'];
	// }

	comment_list($C, $comments, $hl);

	// $forum->set_forum_path($T, $forum_id);

	# TODO: Vajag uztaisīt:
	# 1) lai rāda foruma datus
	# 2) uztaisīt balsošanu par articles un forum
	# 3) pārkopēt foruma pirmā komenta votes uz foruma votēm
	# 4) izvākt pirmo foruma komentu

	Res::markCommentCount($forum_data);

	if($forum_data['forum_closed'])
	{
		$C->disable('BLOCK_addcomment');
		$T->enable('BLOCK_forum_closed');
	}

	$error_msg = [];
	if($action == 'add_comment'){
		if(!User::logged()){
			$template->not_logged();
			return null;
		}

		if($forum_data['forum_closed'])
		{
			$error_msg[] = "Tēma slēgta";
		}

		$data = post('data');
		$C->set_array(specialchars($data));

		if(empty($data['c_data'])){
			$error_msg[] = "Kaut kas jau jāieraksta";
		}

		if(!$error_msg) {
			if($c_id = res_add_comment($res_id, $data['c_data']))
			{
				header("Location: ".Forum::RouteFromRes($forum_data, $c_id));
				return null;
			} else {
				$error_msg[] = "Neizdevās pievienot komentāru";
			}
		}
	}

	if($error_msg) {
		$C->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	$T->set_block_string($C->parse(), 'BLOCK_forum_comments');

	# Attendees
	if(User::logged() && ($forum_data['type_id'] == Res::TYPE_EVENT) && ($A = attendees($template, $forum_data)))
	{
		$T->set_block_string($A->parse(), 'BLOCK_attend');
	}

	return $T;
}

function get_res_comments(int $res_id, array $params = [])
{
	$sql = (new Select("comment.*, res.*, res_meta.*"))
		->From('res', 'parent_res')
		->Join('res', 'res.res_resid = parent_res.res_id')
		->Join('res_meta', 'res_meta.res_id = res.res_id')
		->Join('comment', 'comment.res_id = res.res_id')
		->Where(["parent_res.res_id = ?", $res_id])
	;

	join_logins($sql);

	// if(isset($params['c_id'])){
	// 	$sql->Where(["comment.c_id = ?", $params['c_id']]);
	// }

	if(isset($params['login_id'])){
		$sql->Where(["res.login_id = ?", $params['login_id']]);
	}

	if(isset($params['c_visible'])){
		new TODO('c_visible: use res_visible');
	}

	if(defaulted($params, 'res_visible'))
	{
		$sql->Where("res.res_visible = 1");
	} elseif(!ignored($params, 'res_visible')){
		$sql->Where(["res.res_visible = ?", $params['res_visible']]);
	}

	if(isset($params['ips']))
	{
		if(is_array($params['ips']))
		{
			$sql->WhereIn('res.res_ip', $params['ips']);
		} else {
			# TODO: tikai viena tipa arg - vai nu array vai string!!
			$sql->Where('res.res_ip = ?', $params['ips']);
		}
	}

	if(empty($params['order'])) {
		$sql->OrderBy('res_entered');
	} else {
		$sql->OrderBy($params['order']);
	}

	if(isset($params['rows']))
	{
		$sql->Rows((int)$params['rows']);
	}

	if(isset($params['limit']))
	{
		new TODO("Nodalīt rows un offset");
		// $sql .= " LIMIT $params[limit]";
	}

	return DB::Execute($sql);
}

function comment_list(Template $C, array $comments, string $hl): void
{
	if(User::logged())
	{
		$C->enable('BLOCK_comment_form');
		$disabled_users = CommentDisabled::get(User::id());
	} else {
		$C->enable('BLOCK_notloggedin');
		$disabled_users = array();
	}

	if($comments)
	{
		$BLOCK_comment = $C->enable('BLOCK_comment');
	} else {
		$C->enable('BLOCK_nocomment');
	}

	$comment_nr = 0;

	foreach($comments as $item)
	{
		$comment_nr++;
		// $item['res_votes'] = (int)$item['res_votes'];

		# balsošana
		if(User::logged() && $BLOCK_comment->block_exists('BLOCK_comment_vote')){
			$BLOCK_comment->enable('BLOCK_comment_vote');
		}

		if($hl){
			$item['res_data_compiled'] = hl($item['res_data_compiled'], $hl);
		}

		$item['res_nickname'] = specialchars($item['res_nickname']);
		if(empty($disabled_users[$item['login_id']])){
			$BLOCK_comment->set_var('c_disabled_user_class', '');
		} else {
			$BLOCK_comment->set_var('c_disabled_user_class', ' disabled');
			$item['res_data_compiled'] = '-neredzams komentārs-';
		}

		$BLOCK_comment->set_array($item);
		$BLOCK_comment->set_var('res_date', proc_date($item['res_entered']));
		$BLOCK_comment->set_var('res_votes', format_vote($item['res_votes']));
		$BLOCK_comment->set_var('comment_vote_class', comment_vote_class($item['res_votes']));

		// Joined from logins
		// if(User::logged() && ($item['l_login'] || $item['c_userlogin'] || $item['login_id'])){
		if(User::logged() && $item['l_hash']){
			$BLOCK_comment->set_var('l_hash', $item['l_hash']);
			$BLOCK_comment->enable('BLOCK_profile_link');
		} else {
			$BLOCK_comment->disable('BLOCK_profile_link');
		}

		// if($item['l_login'])
		// 	$C->set_var('user_login_id', $item['l_login']);
		// elseif($item['c_userlogin']) // legacy
		// 	$C->set_var('user_login_id', $item['c_userlogin']);
		// elseif($item['login_id']) // legacy
		// 	$C->set_var('user_login_id', $item['login_id']);

		// if(User::logged() && ($item['c_userlogin'] || $item['login_id']))
		// 	$C->enable('BLOCK_profile_link');
		// else
		// 	$C->disable('BLOCK_profile_link');

		$BLOCK_comment->set_var('comment_nr', $comment_nr);

		$BLOCK_comment->parse(TMPL_APPEND);
	}
}

function get_attendees(int $res_id){
	$sql = "SELECT a.*, l.l_nick, l.l_hash
	FROM attend a
	JOIN logins l ON l.l_id = a.l_id
	WHERE res_id = $res_id
	ORDER BY a_entered";

	return DB::Execute($sql);
}

// TODO: izvākt is_private
function set_profile(Template $T, LoginsType $login, $is_private = false)
{
	global $sys_user_root;

	// $login['l_forumsort_themes'] = isset($login['l_forumsort_themes']) ? $login['l_forumsort_themes'] : Forum::SORT_LASTCOMMENT;
	// $login['l_forumsort_msg'] = isset($login['l_forumsort_msg']) ? $login['l_forumsort_msg'] : Forum::SORT_ASC;
	$pic_localpath = $sys_user_root."/pic/$login->l_id.jpg";
	$tpic_localpath = $sys_user_root."/pic/thumb/$login->l_id.jpg";

	$T->set_except(['l_password', 'l_sessiondata'], $login);

	$T->set_var("l_forumsort_themes_$login->l_forumsort_themes", ' checked="checked"');
	$T->set_var("l_forumsort_msg_$login->l_forumsort_msg", ' checked="checked"');
	$T->set_var('l_disable_youtube_checked', $login->l_disable_youtube ? ' checked="checked"' : "");
	$T->set_var('l_emailvisible', $login->l_emailvisible ? ' checked="checked"' : "");

	if(file_exists($pic_localpath) && file_exists($tpic_localpath))
	{
		$T->set_var('thumb_path', "/user/thumb/$login->l_hash/");
		$T->enable('BLOCK_picture');
		if($is_private){
			$T->enable('BLOCK_picture_del');
		}
	} else {
		$T->enable('BLOCK_nopicture');
	}

	$formatter = new IntlDateFormatter("lv", IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
	$days = floor((time() - strtotime($login->l_lastaccess)) / (3600 * 24));

	if(!$days){
		$l_lastaccess_f = "šodien";
	} elseif($days == 1){
		$l_lastaccess_f = "vakar";
	} elseif($days == 2){
		$l_lastaccess_f = "aizvakar";
	} elseif($days == 3){
		$l_lastaccess_f = "pirms trim dienām";
	} elseif($days < 365){
		$l_lastaccess_f = "pirms $days ".($days % 10 == 1 ? "dienas" : "dienām");
	} else {
		$l_lastaccess_f = $formatter->format(strtotime($login->l_lastaccess));
	}

	$T->set_var('l_lastaccess_f', $l_lastaccess_f);
	$T->set_var('l_entered_f', $formatter->format(strtotime($login->l_entered)));
}

function public_profile(MainModule $template, string $l_hash): ?Template
{
	$action = post('action');

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($login_data = Logins::load_by_login_hash($l_hash))){
		$template->not_found();
		return null;
	}

	$T = $template->add_file('user/profile/user.tpl');

	# Disable comments
	if(
		($action == 'disable_comments') &&
		(User::id() != $login_data->l_id)
		)
	{
		if(isset($_POST['disable_comments']))
		{
			$ret = CommentDisabled::disable(User::id(), $login_data->l_id);
		} else {
			$ret = CommentDisabled::enable(User::id(), $login_data->l_id);
		}

		if($ret)
		{
			if(isset($_SERVER['HTTP_REFERER']))
				redirect($_SERVER['HTTP_REFERER']);
			else
				redirect();
			return null;
		}
	}

	if(CommentDisabled::get(User::id(), $login_data->l_id))
	{
		$T->set_var('disable_comments_checked', ' checked="checked"');
	} else {
		$T->set_var('disable_comments_checked', '');
	}

	if(User::id() != $login_data->l_id)
	{
		$T->enable('BLOCK_disable_comments');
	}

	$template->set_title(" - $login_data->l_nick");
	if($login_data->l_emailvisible)
	{
		$T->enable('BLOCK_public_email');
	} else {
		$T->enable('BLOCK_public_email_invisible');
	}

	set_profile($T, $login_data);

	return $T;
}

function private_profile(MainModule $template): ?Template
{
	global $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

	$module_root = "/user/profile";

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	$action = get('action');

	# del image
	if($action == 'deleteimage')
	{
		if(Logins::delete_image())
		{
			header("Location: $module_root/");
			return null;
		} else {
			$template->error('Bildi neizdevās izdzēst!');
		}
	}

	$post_data = post('data', []);
	if($post_data || $_FILES)
	{
		if(update_profile($template, $post_data))
		{
			header("Location: $module_root/");
			return null;
		}
		$login_data = LoginsType::initFrom($post_data, User::data());
	} else {
		$login_data = LoginsType::initFrom(User::data());
	}

	$T = $template->add_file('user/profile/private.tpl');

	$set_vars = array(
		'user_pic_w'=>$user_pic_w,
		'user_pic_h'=>$user_pic_h,
		'user_pic_tw'=>$user_pic_tw,
		'user_pic_th'=>$user_pic_th
	);

	$T->set_array($set_vars);

	set_profile($T, $login_data, true);

	$top_comments = [];
	for($r=0; $r<2; $r++)
	{
		if($r == 0){
			$v = 'res_meta.res_votes_plus_count DESC';
		} elseif($r == 1){
			$v = 'res_meta.res_votes_minus_count DESC';
		}

		$sql = "SELECT comment.*, res.*, res_meta.* FROM res
		JOIN res_meta ON res_meta.res_id = res.res_id
		JOIN comment ON comment.res_id = res.res_id
		WHERE res.login_id = ? AND res.res_visible = 1
		ORDER BY $v
		LIMIT 10";

		$top_comments[$r] = DB::Execute($sql, User::id());
	}

	if($top_comments[0] || $top_comments[1]){
		$T->enable('BLOCK_truecomments');
	}

	foreach($top_comments as $r=>$data){
		if (!$data){
			continue;
		}

		if($r == 0){
			$T->set_var('truecomment_msg', 'Visvairāk plusotie komenti:');
		} elseif($r == 1){
			$T->set_var('truecomment_msg', 'Visvairāk mīnusotie komenti:');
		} else {
			assert(false, "unreachable");
		}

		$BLOCK_truecomment_item = $T->get_block('BLOCK_truecomment_item');
		foreach($data as $item)
		{
			$res_data = $item['res_data'];
			if(mb_strlen($res_data) > 70){
				$res_data = mb_substr($res_data, 0, 70).'...';
			}

			$res_href = "/resroute/{$item['res_resid']}/?c_id={$item['c_id']}";

			$BLOCK_truecomment_item
			->set_var('res_votes_plus_count', $item['res_votes_plus_count'])
			->set_var('res_votes_minus_count', $item['res_votes_minus_count'])
			->set_var('res_data', $res_data)
			->set_var('res_href', $res_href)
			->parse(TMPL_APPEND);

			//$template->disable('BLOCK_truecomment_header');
		}
		$T->parse_block('BLOCK_truecomments', TMPL_APPEND);
	}

	// Passw status
	$sql = sprintf("SELECT bp.*
	FROM logins l
	JOIN bad_pass bp ON bp.pass_hash = l.l_password
	WHERE l.l_id = %d", User::id());

	if($data = DB::ExecuteSingle($sql)){
		$T->set_var('bad_pass_class', 'blink');
		$T->set_var('bad_pass_style', 'color: red');
		if($data['is_dict'] && $data['is_brute']){
			$msg = "Apsveicam! Tava parole ir gan paroļu vārdnīcā gan viegli atlaužama! Nomaini!";
		} elseif($data['is_dict']) {
			$msg = "Tava parole ir paroļu vārdnīcā! Nomaini!";
		} elseif($data['is_brute']) {
			$msg = "Tava parole ir viegli atlaužama! Nomaini!";
		}
	} else {
		$T->set_var('bad_pass_style', 'color: #00a400');
		$msg = "Apsveicam! Tava parole nav paroļu vārdnīcā vai viegli atlaužama!";
	}

	$T->set_var('bad_pass_msg', $msg);

	return $T;
}

function email(string $to, string $subj, string $msg, array $attachments = array())
{
	global $sys_mail, $sys_public_root, $sys_mail_params;

	$params = new StdClass;
	$params->MAIL_PARAMS = $sys_mail_params;
	$params->use_queue = 0;
	// $params->delete_after_send = false;
	// $params->id_user = $r->ClientId;
	$params->to = $to;
	$params->from = $sys_mail;

	$params->isHTML = false;
	$params->basedir = $sys_public_root;
	$params->message = $msg;
	// $params->messageTxt = strip_tags($params->message);
	$params->subject = $subj;
	$params->attachments = $attachments;

	return emailex($params);
}

function change_email(MainModule $template): ?Template
{
	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	$old_email = trim(User::email());

	$T = $template->add_file('user/emailch.tpl');
	$T->set_var('old_email', specialchars($old_email));

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$data = $_POST['data'];

	$new_email = trim($data['new_email'] ?? "");

	$error_msg = [];
	if(empty($new_email)){
		$error_msg[] = "Nav norādīts jaunais e-pasts";
	} else {
		if(strtolower($new_email) == strtolower($old_email)){
			$error_msg[] = "Jaunais e-pasts nav jauns";
		} else {
			if(!is_valid_email($new_email)) {
				$error_msg[] = 'Nekorekta e-pasta adrese!';
			} else {
				if(Logins::email_exists($new_email)){
					$error_msg[] = 'Šāda e-pasta adrese jau eksistē';
				}
			}
		}
	}

	$do_code = function(string $l_email, string $new_email, array &$error_msg): bool {
		global $sys_domain;

		$accept_code = Logins::insert_accept_code($l_email, $new_email);

		if(!$accept_code){
			$error_msg[] = "Datubāzes kļūda";
			return false;
		}

		$t = new_template('emails/email_changed.tpl');
		$t->set_var('ip', $_SERVER['REMOTE_ADDR']);
		$t->set_var('sys_domain', $sys_domain);
		$t->set_var('code', $accept_code);
		$msg = $t->parse();

		$subj = "$sys_domain - e-pasta apstiprināšana";

		try {
			if(Logins::send_accept_code($l_email, $new_email, $subj, $msg))
			{
				return true;
			} else {
				$error_msg[] = "Nevar nosūtīt kodu uz $new_email";
			}
		} catch (Exception $e) {
			$error_msg[] = "Nevar nosūtīt kodu uz $new_email<br>".$e->getMessage();
		}

		return false;
	};

	$result = !$error_msg && $do_code(User::email(), $new_email, $error_msg);

	if($result)
	{
		$template->msg("Uz $new_email tika nosūtīts apstiprināšanas kods.");
		return null;
	} else {
		$T->set_var('error_new_email', ' class="error-form"');
		$T->set_var('new_email', specialchars($new_email));

		$template->error($error_msg);
		return $T;
	}
}

function change_pw(MainModule $template): ?Template
{
	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	$T = $template->add_file('user/pwch.tpl');

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$data = $_POST['data'];
	$T->set_array($data);

	$error_fields = $error_msgs = [];

	if(empty($data['old_password'])){
		$error_msgs[] = 'Nav ievadīta vecā parole';
		$error_fields[] = 'old_password';
	} else {
		if(Logins::auth(User::login(), $_POST['data']['old_password'])){
			if(!pw_validate($data['l_password'], $data['l_password2'], $error_msgs)){
				$error_fields[] = 'l_password';
			}
		} else {
			$error_msgs[] = 'Vecā parole nav pareiza';
			$error_fields[] = 'old_password';
		}
	}

	if(!$error_msgs){
		if((new Logins)->update_password(User::login(), $data['l_password'])){
			$template->msg("Parole nomainīta.");
			return null;
		} else {
			$error_msgs[] = "Datubāzes kļūda";
		}
	}

	if($error_msgs){
		foreach($error_fields as $k){
			$T->set_var('error_'.$k, ' class="error-form"');
		}

		$template->error($error_msgs);
	}

	return $T;
}

function forgot(MainModule $template): ?Template {
	$T = $template->add_file('forgot.tpl');
	$T->enable('BLOCK_forgot_form');

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$data = $_POST['data'];
	$T->set_array($data);

	if(empty($data['l_email']) && empty($data['l_login']))
	{
		$error_msg[] = 'Jānorāda logins vai e-pasts';
	} else {
		if($data['l_login'])
		{
			$L = Logins::load_by_login($data['l_login']);
		} elseif($data['l_email']) {
			$L = Logins::load_by_email($data['l_email']);
		}

		if(empty($L))
		{
			$error_msg[] = 'Lietotājs netika atrasts vai ir bloķēts';
		}
	}

	if(empty($error_msg))
	{
		if($forgot_code = Logins::insert_forgot_code($L->l_email)){
			try {
				if(Logins::send_forgot_code($L->l_email, $forgot_code))
				{
					$T->set_var('l_email', $L->l_email);
					$T->enable('BLOCK_forgot_ok');
					$T->disable('BLOCK_forgot_form');
				} else {
					$error_msg[] = "Nevar nosūtīt kodu uz $L->l_email";
				}
			} catch (Exception $e) {
				$error_msg[] = "Nevar nosūtīt kodu uz $L->l_email<br>".$e->getMessage();
			}
		} else {
			$error_msg[] = "Datubāzes kļūda";
		}
	}

	if(!empty($error_msg))
	{
		$template->error($error_msg);
	}

	return $T;
}

function forgot_accept(MainModule $template, string $code): ?Template
{
	$T = $template->add_file('forgot.tpl');

	if($code == 'ok')
	{
		$template->msg("Parole nomainīta! Tagad tu vari mēģināt ielogoties!");
		return null;
	}

	$forgot_data = Logins::get_forgot($code);

	if(!$forgot_data)
	{
		$T->enable('BLOCK_forgot_code_error');
		return $T;
	}

	$L = Logins::load_by_email($forgot_data['f_email']);

	if(empty($L))
	{
		$template->error('Lietotājs netika atrasts vai ir bloķēts');
		return $T;
	}

	$T->enable('BLOCK_forgot_pwch_form');
	$T->set_except(['l_password', 'l_sessiondata'], $L);

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$data = $_POST['data'];
	$error_msg = [];
	pw_validate($data['l_password']??"", $data['l_password2']??"", $error_msg);

	if($error_msg){
		$template->error($error_msg);
	} else {
		$OK = DB::withNewTrans(function() use($L, $code, $data) {
			return
				Logins::accept($L->l_id) &&
		Logins::remove_forgot_code($code) &&
				Logins::update_password($L->l_login, $data['l_password'])
			;
		});

		if($OK) {
		header("Location: /forgot/accept/ok/");
		return null;
	}
	}

	$T->set_array($data);

	return $T;
}

function register(MainModule $template, array $sys_parameters = []): ?Template
{
	global $sys_mail;

	$T = $template->add_file('register.tpl');

	$action = array_shift($sys_parameters)??"";

	if($action == 'ok')
	{
		$T->enable('BLOCK_register_ok');
		return $T;
	}

	if($action == 'accept') {
		$code = array_shift($sys_parameters)??"";
		if(Logins::accept_login($code))
		{
			$T->enable('BLOCK_accept_ok');
		} else {
			$T->enable('BLOCK_accept_error');
		}
		return $T;
	}

	$T->enable('BLOCK_register_form');

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$check = [ 'l_login', 'l_nick', 'l_password', 'l_email' ];
	$data = $_POST['data'];

	$error_msg = $error_field = [];
	foreach($check as $c) {
		$data[$c] = isset($data[$c]) ? trim($data[$c]) : '';
		if(empty($data[$c])){
			$error_field[] = $c;
		}
	}

	$data['l_login'] = strtolower($data['l_login']);
	$data['l_email'] = strtolower($data['l_email']);

	if(empty($data['l_password'])){
		$error_msg[] = 'Nav ievadīta parole';
		$error_field[] = 'l_password';
		$error_field[] = 'l_password2';
	} else {
		if(!pw_validate($data['l_password'], $data['l_password2'], $error_msg)){
			$error_field[] = 'l_password';
			$error_field[] = 'l_password2';
		}
	}

	if(invalid($data['l_login']) || strlen($data['l_login']) < 5) {
		$error_msg[] = 'Neatļauti simboli vai īss logins';
		$error_field[] = 'l_login';
	}

	if(!is_valid_email($data['l_email'])) {
		$error_msg[] = 'Nekorekta e-pasta adrese';
		$error_field[] = 'l_email';
	}

	if(Logins::login_exists($data['l_login'])) {
		$error_field[] = 'l_login';
		$error_msg[] = 'Šāds login jau eksistē';
	}

	if(Logins::email_exists($data['l_email'])){
		$error_field[] = 'l_email';
		$error_msg[] = 'Šāda e-pasta adrese jau eksistē';
	}

	if(Logins::nick_exists($data['l_nick'])) {
		$error_field[] = 'l_nick';
		$error_msg[] = 'Šāds segvārds jau eksistē';
	}

	if(!$error_msg && !$error_field)
	{
		if(Logins::register($data)){
			try {
				email($sys_mail, '[truemetal] jauns lietotajs', "$data[l_login] ($data[l_nick])\n\nIP:$_SERVER[REMOTE_ADDR]");
			} catch (Exception $e) {
			}
			header("Location: /register/ok/");
			return null;
		} else {
			$error_msg[] = "Datubāzes kļūda";
		}
	}

	$T->set_array(specialchars($data));

	if($error_msg)
	{
		$template->error($error_msg);
	}

	set_error_fields($T, $error_field);

	return $T;
}

function set_error_fields(TemplateBlock $T, array $fields){
	foreach($fields as $k)
	{
		$T->set_var('error_'.$k, ' class="error-form"');
	}
}

# TODO: varbūt vajadzētu kaut kā apvienot daudzās vietas, kur šis tiek izsaukt, jo
# patlaban datu validācija notiek katrā izsaukšanas vietā
function res_add_comment(int $res_id, string $c_data)
{
	$R = prepare_res(
		res_resid: $res_id,
		table_id: Table::COMMENT,
		res_data: $c_data,
	);

	return DB::withNewTrans(function() use ($R){
		if($res_id = $R->insert()){
			return CommentType::initFrom(new CommentDummy(
				res_id: $res_id
			))->insert();
		}
	});
}

function forum_root(MainModule $template): Template
{
	$forum_data = (new Forum())->load([
		"res_resid"=>0,
		"order"=>"forum_id ASC",
	]);

	$T = $template->add_file('forum.tpl');

	if(empty($forum_data))
	{
		$T->enable('BLOCK_noforum');
		return $T;
	}

	$T->enable('BLOCK_forum');
	foreach($forum_data as $item)
	{
		$T->enable_if(Forum::hasNewThemes($item), 'BLOCK_comments_new');
		$T->set_array($item);
		$T->set_var('res_route', Forum::RouteFromRes($item));
		$T->set_var('forum_date', proc_date($item['res_entered']));
		$T->parse_block('BLOCK_forum', TMPL_APPEND);
	}

	return $T;
}

function user_image(string $l_hash, bool $thumb = false)
{
	global $sys_user_root, $sys_public_root;

	if(!User::logged()){
		header403();
		return;
	}

	$suffix = "";

	$login_data = Logins::load_by_login_hash($l_hash);
	if(empty($login_data)){
		header404();
		return;
	}

	$parts = [$sys_user_root, "pic"];
	if($thumb)$parts[] = "thumb";
	$parts[] = "$login_data->l_id$suffix.jpg";

	$pic_localpath = join(DIRECTORY_SEPARATOR, $parts);

	if($info = getimagesize($pic_localpath))
	{
		$last_modified_time = filemtime($pic_localpath);
		$etag = md5_file($pic_localpath);
		$image_data = file_get_contents($pic_localpath);

		header("Content-type: $info[mime]");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
		header("Etag: $etag");
		header("Expires: ".gmdate("D, d M Y H:i:s", time() + (7 * 24 * 3600)) . " GMT"); // 7d NOTE: keep in sync with web server

		$not_mod = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time);
		$non_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag);

		if($not_mod || $non_match)
		{
			header("HTTP/1.1 304 Not Modified");
			return;
		}
		print $image_data;
	} else {
		header("Content-type: image/gif");
		readfile("$sys_public_root/img/1x1.gif");
	}
}

function whatsnew(MainModule $template): ?Template
{
	$T = $template->add_file('whatsnew.tpl');

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	# Forum
	$data = Forum::load([
		"order"=>'res_comment_last_date DESC',
		"rows"=>50,
		"forum_allowchilds"=>0,
	]);

	if($data)
	{
		$R = new_template('forum/recent.tpl');
		foreach($data as $item)
		{
			$R->enable_if(Res::hasNewComments($item), 'BLOCK_forum_r_comments_new');
			$R->set_var('res_name', specialchars($item['res_name']));
			$R->set_var('res_comment_count', $item['res_comment_count']);
			$R->set_var('res_route', Forum::RouteFromRes($item));
			$R->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
		}
		$T->set_block_string($R->parse(), 'BLOCK_whatsnew_forum');
	}

	# Articles
	$data = Article::load([
		'order'=>'res_comment_last_date DESC',
		'rows'=>50,
	]);

	if($data)
	{
		$R = new_template('right/comment_recent.tpl');
		foreach($data as $item)
		{
			$R->enable_if(Res::hasNewComments($item), 'BLOCK_comment_r_comments_new');

			$R->set_var('res_name', specialchars($item['res_name']));
			$R->set_var('res_comment_count', $item['res_comment_count']);
			$R->set_var('res_route', Article::RouteFromRes($item));
			$R->parse_block('BLOCK_comment_r_items', TMPL_APPEND);
		}
		$T->set_block_string($R->parse(), 'BLOCK_whatsnew_comments');
	}

	return $T;
}

function user_comments(MainModule $template, string $l_hash, string $hl): ?Template
{
	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($login_data = Logins::load_by_login_hash($l_hash)))
	{
		$template->not_found();
		return null;
	}

	$T = $template->add_file('user/comments.tpl');
	$C = new_template('comments.tpl');

	// $T->set_array($login_data);

	# Comments
	$params = [
		'login_id'=>$login_data->l_id,
		'rows'=>100,
		'order'=>"res_entered DESC",
	];

	$comments = Comment::load($params);

	comment_list($C, $comments, $hl);

	$C->disable('BLOCK_addcomment');

	$T->set_block_string($C->parse(), 'BLOCK_user_comments_list');

	return $T;
}

function gallery_thumbs_list(MainModule $template, int $gal_id): ?Template
{
	global $CACHE_ENABLE;

	$gallery = new Gallery;
	if(!($gal = $gallery->load($gal_id))){
		$template->not_found();
		return null;
	}

	$GD = new GalleryData;
	$T = $template->add_file('gallery.tpl');

	// # ja skataas bildi, nocheko vai attieciigaa galerija ir pieejama
	// if($gal_id == 'view' && $gd_id)
	// {
	// 	$galdata = $GD->load($gd_id);
	// 	if(!isset($galdata['gal_id'])) {
	// 		header("Location: /");
	// 		exit;
	// 	}
	// 	$gal = $gallery->load($galdata['gal_id']);
	// } else {
	// 	$gal = $gallery->load($gal_id);
	// }

	$gal_name = "";
	if($gal['gal_ggid'])
		$gal_name .= "$gal[gg_name] / ";
	$gal_name .= "$gal[gal_name]";

	$T->set_var('gal_name', $gal_name);
	$T->set_var('gal_id', $gal['gal_id']);
	$template->set_title('Galerija '.$gal_name);

	if($gal['gal_ggid']){
		$T->set_var('gal_jump_id', "gg_".$gal['gal_ggid']);
	} else {
		$T->set_var('gal_jump_id', "gal_".$gal['gal_id']);
	}

	$T->enable('BLOCK_thumb_list');

	# ielasam thumbus
	$tpr = 5;
	$c = 0;
	$data = $GD->load(['gal_id'=>$gal_id]);
	$thumb_count = count($data);
	foreach($data as $thumb)
	{
		++$c;
		if($c % $tpr == 1)
			$T->enable('BLOCK_tr1');
		else
			$T->disable('BLOCK_tr1');
		if(($c % $tpr == 0) || ($c == $thumb_count))
			$T->enable('BLOCK_tr2');
		else
			$T->disable('BLOCK_tr2');

		if($CACHE_ENABLE && ($hash = cache_hash($thumb['gd_id']."thumb.jpg")) && cache_exists($hash)){
			$T->set_var('thumb_path', cache_http_path($hash), 'BLOCK_thumb');
		} else {
			$T->set_var('thumb_path', "/gallery/thumb/$thumb[gd_id]/", 'BLOCK_thumb');
		}

		$T->enable_if(GalleryData::hasNewComments($thumb), 'BLOCK_comments_new');

		if($thumb['res_votes'] > 0)
		{
			$T->set_var('comment_vote_class', 'plus', 'BLOCK_thumb');
			$thumb['res_votes'] = '+'.$thumb['res_votes'];
		} elseif($thumb['res_votes'] < 0) {
			$T->set_var('comment_vote_class', 'minus', 'BLOCK_thumb');
		} else {
			$T->set_var('comment_vote_class', '', 'BLOCK_thumb');
		}
		$T->set_array($thumb, 'BLOCK_thumb');
		$T->parse_block('BLOCK_thumb', TMPL_APPEND);
	}

	return $T;
}


function gallery_root(MainModule $template): ?Template
{
	$gallery = new Gallery;
	if(!($data = $gallery->load()))
	{
		$template->not_found();
		return null;
	}

	$T = $template->add_file('gallery.tpl');

	$T->enable('BLOCK_gallery_list');

	$data2 = array();
	foreach($data as $gal) {
		$k = empty($gal['gal_ggid']) ? "e-".$gal['gal_id'] : $gal['gal_ggid'];
		$data2[$k][] = $gal;
	}

	foreach($data2 as $data)
	{
		$T->set_array($data[0], 'BLOCK_gallery_list');
		if($data[0]['gal_ggid']){
			$T->set_var('gg_name', $data[0]['gg_name'], 'BLOCK_gallery_group');
			$T->set_var('gal_jump_id', "gg_".$data[0]['gg_id'], 'BLOCK_gallery_group');
		} else {
			$T->set_var('gg_name', $data[0]['gal_name'], 'BLOCK_gallery_group');
			$T->set_var('gal_jump_id', "gal_".$data[0]['gal_id'], 'BLOCK_gallery_group');
		}

		foreach($data as $gal){
			$T->set_array($gal, 'BLOCK_gallery_data');
			$T->parse_block('BLOCK_gallery_data', TMPL_APPEND);
		}
		$T->parse_block('BLOCK_gallery_list', TMPL_APPEND);
	}

	return $T;
}

function gallery_image(int $gd_id, string $gal_type): void
{
	global $CACHE_ENABLE;

	$GD = new GalleryData;

	if($CACHE_ENABLE){
		$hash = cache_hash($gd_id.$gal_type.".jpg");
	}

	if($CACHE_ENABLE && cache_exists($hash)){
		$jpeg = cache_read($hash);
	} else {
		$data = $GD->load(['gd_id'=>$gd_id, 'load_images'=>true]);
		$jpeg = $gal_type == 'image' ? $data['gd_data'] : $data['gd_thumb'];

		if($CACHE_ENABLE && $jpeg)
			cache_save($hash, $jpeg);
	}

	header("Content-type: image/jpeg");
	print $jpeg;
}

function gallery_view(MainModule $template, int $gd_id): ?Template
{
	global $CACHE_ENABLE;

	$action = post('action');

	$GD = new GalleryData;

	if(!($galdata = $GD->load($gd_id))){
		$template->not_found();
		return null;
	}

	$gallery = new Gallery;
	$gal = $gallery->load($galdata['gal_id']);

	# Komenti
	Res::markCommentCount($galdata);

	$T = $template->add_file('gallery.tpl');
	$C = new_template('comments.tpl');

	$error_msg = [];
	if($action == 'add_comment')
	{
		$data = post('data');
		$C->set_array(specialchars($data));

		if(empty($data['c_data'])){
			$error_msg[] = "Kaut kas jau jāieraksta";
		}

		if(empty($error_msg)){
			$res_id = (int)$galdata['res_id'];
			$data = post('data');
			if($c_id = res_add_comment($res_id, $data['c_data']))
			{
				header("Location: ".GalleryData::Route($galdata, $c_id));
				return null;
			} else {
				$error_msg[] = "Never pievienot komentāru";
			}
		}
	}

	if($error_msg)
	{
		$C->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}


	$params = array('res_id'=>$galdata['res_id']);

	# TODO: izvākt un ielikt kaut kur zem list.inc.php
	$params['order'] = User::get_val('l_forumsort_msg') == Forum::SORT_DESC
		? "c_entered DESC"
		: "c_entered";

	$comments = (new ResComment)->Get($params);
	comment_list($C, $comments, "");
	$T->set_block_string($C->parse(), 'BLOCK_gallery_comments');

	# ja skataas pa vienai
	$T->enable('BLOCK_image');

	if($CACHE_ENABLE && ($hash = cache_hash($gd_id."image.jpg")) && cache_exists($hash)){
		$T->set_var('image_path', cache_http_path($hash), 'BLOCK_image');
	} else {
		$T->set_var('image_path', "/gallery/image/$gd_id/", 'BLOCK_image');
	}

	$galdata['res_votes'] = (int)$galdata['res_votes'];
	if($galdata['res_votes'] > 0)
	{
		$T->set_var('comment_vote_class', 'plus', 'BLOCK_image');
		$galdata['res_votes'] = '+'.$galdata['res_votes'];
	} elseif($galdata['res_votes'] < 0) {
		$T->set_var('comment_vote_class', 'minus', 'BLOCK_image');
	} else {
		$T->set_var('comment_vote_class', '', 'BLOCK_image');
	}

	# nechekojam, vai ir veel bildes
	$next_id = $GD->get_next_data($gal['gal_id'], $gd_id);
	$T->set_var('gd_nextid', $next_id ? $next_id : $gd_id, 'BLOCK_image');

	$T->set_array($galdata, 'BLOCK_image');

	return $T;
}

function admin_comment_list(
	Template $C,
	array $comments
){

	if($comments)
	{
		$C->enable('BLOCK_comments');
	} else {
		$C->enable('BLOCK_nocomments');
	}

	foreach($comments as $item)
	{
		$item['c_origin_href'] = "/resroute/$item[parent_res_id]/?c_id=$item[c_id]";
		$item['c_origin_name'] = "#comment$item[c_id]";

		$C->set_array($item, 'BLOCK_comment_item');

		if($item['c_visible'] == Res::STATE_VISIBLE)
		{
			$C->enable('BLOCK_c_visible');
			$C->disable('BLOCK_c_invisible');
			$C->set_var('c_color_class', 'box-normal', 'BLOCK_comment_item');
		} else {
			$C->enable('BLOCK_c_invisible');
			$C->disable('BLOCK_c_visible');
			$C->set_var('c_color_class', 'box-invisible', 'BLOCK_comment_item');
		}
		$C->parse_block('BLOCK_comment_item', TMPL_APPEND);
	}
}

function vote(MainModule $template, string $value, int $res_id): ?TrueResponseInterface
{
	global $ip;

	$json = isset($_GET['json']);

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($res_data = Res::load(['res_id'=>$res_id]))){
		$template->not_found();
		return null;
	}

	$login_id = User::id();
	$res_login_id = (int)$res_data['login_id'];

	if($res_login_id == $login_id)
	{
		$msg = specialchars($value == 'up' ? ":)" : ">:)");
		$template->msg($msg);
		return null;
	}

	# Check count
	$date = date('Y-m-d H:i:s', time() - 24 * 3600); // 24h
	$check_sql = sprintf(
		"SELECT COUNT(*) cv_count FROM `res_vote` WHERE login_id = %d AND rv_entered >= '%s'",
		$login_id,
		$date
	);

	$countCheck = DB::ExecuteSingle($check_sql);
	if($countCheck['cv_count'] >= 24)
	{
		$template->msg("Pārsniegtiņš divdesmitčetriņu stundiņu limitiņš balsošaniņai.");
		return null;
	}

	# Insert
	$insert_sql = sprintf(
		"INSERT IGNORE INTO res_vote (res_id, login_id, rv_value, rv_userip, rv_entered) VALUES (%d, %d, %d, '%s', NOW())",
		$res_id,
		$login_id,
		$value == 'up' ? 1 : -1,
		$ip
	);

	if(!DB::Execute($insert_sql))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if(!($new_data = Res::load(['res_id'=>$res_id])))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($json){
		$retJson = new StdClass;
		$retJson->Votes = $new_data['res_votes'];
		return new JsonResponse($retJson);
	} else {
		redirect_referer();
		return null;
	}
}

function attend(MainModule $template, int $res_id, ?string $off = null): ?TrueResponseInterface
{
	$json = isset($_GET['json']);

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($item = Res::GetAll($res_id)))
	{
		$template->not_found();
		return null;
	}

	$login_id = User::id();

	if($item['type_id'] != Res::TYPE_EVENT)
	{
		$template->forbidden("Nav pasākums");
		return null;
	}

	if(time() > (strtotime(date('d.m.Y', strtotime($item['event_startdate']))) + 24 * 3600)){
		$template->msg("Par vēlu");
		return null;
	}

	if($off == 'off'){
		$sql = "UPDATE attend SET a_attended = 0 WHERE l_id = $login_id AND res_id = $res_id";
	} else {
		$sql = "INSERT INTO attend (l_id, res_id) VALUES ($login_id, $res_id) ON DUPLICATE KEY UPDATE a_attended = 1";
	}

	if(!DB::Execute($sql))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($json){
		return new JsonResponse(["OK"=>true]);
	} else {
		header("Location: /resroute/$res_id/");
		return null;
	}
}

function attendees(MainModule $template, int|array $res): ?Template
{
	$T = $template->add_file('forum/attend.tpl');

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(is_int($res))
	{
		if(!($res = Res::GetAll($res)))
		{
			$template->not_found();
			return null;
		}
	}

	if($res['type_id'] != Res::TYPE_EVENT)
	{
		$template->forbidden("Nav pasākums");
		return null;
	}

	$me_attended = false;
	$T->set_var('res_id', $res['res_id']);
	if($data = get_attendees((int)$res['res_id']))
	{
		$BLOCK_attend_list = $T->enable('BLOCK_attend_list');
		$c = count($data);
		foreach($data as $k=>$item){
			if($item['a_attended'] && (User::id() == $item['l_id'])){
				$me_attended = true;
			}
			if(!$item['a_attended']){
				$item['l_nick'] = "<strike>$item[l_nick]</strike>";
			}
			$BLOCK_attend_list->set_var('l_nick_sep', ($k+1 < $c ? ', ' : ''));
			$BLOCK_attend_list->set_array($item);
			$BLOCK_attend_list->parse(TMPL_APPEND);
		}
	}

	$ts = strtotime(date('d.m.Y', strtotime($res['event_startdate']))) + 24 * 3600;
	if(time() < $ts){
		$T->enable('BLOCK_attend_'.($me_attended ? 'off' : 'on'));
	}

	return $T;
}

function archive(MainModule $template): ?Template
{
	$T = $template->add_file('archive.tpl');

	$q = DB::Query("SELECT * FROM view_mainpage ORDER BY res_entered DESC");

	# ja ir kaadi ieraksti shajaa datumaa, paraadam
	# ja nee, tad paraadam attieciigu pazinjojumu
	if(DB::rowCount() > 0)
		$T->enable('BLOCK_archive_items');
	else
		$T->enable('BLOCK_no_archive');

	$old_date = '';
	// $formatter = new IntlDateFormatter("lv", IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
	$menesi = menesi();

	while($item = DB::Fetch($q))
	{
		$ts = strtotime($item['res_entered']);

		$date = date('Ym', $ts);
		if($old_date && ($old_date != $date))
		{
			$T->enable('BLOCK_archive_sep');
		} else {
			$T->disable('BLOCK_archive_sep');
		}


		if($old_date != $date)
		{
			$res_date = date("Y", $ts).". gada ".mb_strtolower($menesi[date("m", $ts)]);
			$T->enable('BLOCK_archive_date');
			$T->set_var('res_date', $res_date);
			$T->parse_block('BLOCK_archive_date');
			$old_date = $date;
		} else {
			$T->disable('BLOCK_archive_date');
		}

		$T->set_var('res_name', specialchars($item['res_name']));
		$T->set_var('res_route', Res::RouteFromRes($item));
		$T->parse_block('BLOCK_archive_items', TMPL_APPEND);
	}

	return $T;
}

function search_log(MainModule $template): ?Template
{
	$T = $template->add_file('search/log.tpl');

	$sql = "SELECT DISTINCT sl_q FROM `search_log` ORDER BY `sl_id` DESC LIMIT 0,200";
	if(!($q = DB::Query($sql)))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	$B = $T->enable('BLOCK_search_log');

	while($r = DB::Fetch($q))
	{
		$B->set_array(specialchars($r));
		$B->set_var('sl_q_encoded', urlencode($r['sl_q']));
		$B->parse(TMPL_APPEND);
	}

	return $T;
}

function filter_login_data(array $data): array
{
	unset($data['l_sessiondata']);
	unset($data['l_password']);

	return $data;
}

function join_logins(Select $sql)
{
	$sql->Select("logins.l_hash")->LeftJoin("logins", "res.login_id = logins.l_id");
}

function article(MainModule $template, int $art_id, string $hl, ?string $article_route = null): ?Template
{
	# TODO: vote, profile, etc
	if(!($art = Article::load(['art_id'=>$art_id]))){
		$template->not_found();
		return null;
	}

	$action = post('action');

	# NOTE: redirektējam uz jaunajām adresēm, pēc gada (2011-04-30) varēs noņemt
	if($article_route)
	{
		$article_real_route = Article::RouteFromRes($art);
		if(!str_ends_with($article_real_route, "/$article_route"))
		{
			header("Location: $article_real_route", true, 301);
			return null;
		}
	}

	$T = $template->add_file('article.tpl');

	if($hl)
	{
		$art['res_intro'] = hl($art['res_intro'], $hl);
		$art['res_data'] = hl($art['res_data'], $hl);
	}

	# Comments
	$T->enable('BLOCK_article_comments_head');

	$C = $template->add_file('comments.tpl');

	$error_msg = [];
	if($action == 'add_comment')
	{
		if(!User::logged()){
			$template->not_logged();
			return null;
		}

		$res_id = $art['res_id'];
		$data = post('data');
		$C->set_array(specialchars($data));

		if(empty($data['c_data'])){
			$error_msg[] = "Kaut kas jau jāieraksta";
		}

		if(!$error_msg){
			if($c_id = res_add_comment($res_id, $data['c_data']))
			{
				header("Location: ".Article::RouteFromRes($art, $c_id));
				return null;
			} else {
				$error_msg[] = "Neizdevās pievienot komentāru";
			}
		}
	}
	#

	if($error_msg) {
		$C->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	Res::markCommentCount($art);
	$comments = get_res_comments((int)$art['res_id']);
	comment_list($C, $comments, $hl);
	$T->set_block_string($C->parse(), 'BLOCK_article_comments');

	// $art_title .= (isset($art['art_name']) ? " - ".$art['art_name'] : "");

	$art['res_date_f'] = proc_date($art['res_entered']);

	$T->set_array($art);

	return $T;
}

function article_list(MainModule $template, int $page, int $art_per_page)
{
	global $sys_module_id, $module_root;

	# TODO: cache, meta tabulā varbūt? view_mainpage vispār vajadzētu pārģenerēt tikai pēc vajadzības
	$sql = (new Select('COUNT(*) AS cc'))->From('view_mainpage');
	if($sys_module_id != 'article'){
		$sql->Where(['module_id = ?', $sys_module_id]);
	}

	$cc = DB::ExecuteSingle($sql);
	$tc = (int)$cc['cc'];

	$tp = (int)ceil($tc / $art_per_page);
	$art_align = $tc % $art_per_page;

	if(($page < 0) || ($page >= $tp))
	{
		header("Location: $module_root/");
		return;
	}

	$T = $template->add_file('article-list.tpl');

	$sql = (new Select)->From('view_mainpage')
		->OrderBy("res_entered DESC")
		->Rows($art_per_page)
	;

	// if($page){
	// 	$sql->Page($page, $art_per_page);
	// } else {
	// 	$sql->Rows($art_per_page);
	// }

	// if($page)
	// 	$limit = (($tp - $page - 1) * $art_per_page + $art_align).",$art_per_page";
	// else
	// 	$limit = $art_per_page;

	if($page){
		$limit = (($tp - $page - 1) * $art_per_page + $art_align);
		$sql->Offset($limit);
	}

	if($sys_module_id != 'article'){
		$sql->Where(['module_id = ?', $sys_module_id]);
	}

	// $sql .= " ORDER BY res_entered DESC";
	// $sql .= " LIMIT $limit";

	if(!($articles = DB::Execute($sql))){
		$template->not_found();
		return null;
	}

	# Pages
	if($tc)
	{
		$T->enable('BLOCK_article_page');

		if($page)
		{
			if($page == $tp){
				$T->enable('BLOCK_article_page_next');
				$T->set_var('page', '', 'BLOCK_article_page_next');
			} else if($page < $tp){
				$T->enable('BLOCK_article_page_next');
				$T->set_var('page', "$module_root/page/".($page + 1)."/", 'BLOCK_article_page_next');
			}

			if($page > 1){
				$T->enable('BLOCK_article_page_prev');
				$T->set_var('page', "$module_root/page/".($page - 1)."/", 'BLOCK_article_page_prev');
			}
		} else {
			$T->enable('BLOCK_article_page_prev');
			$T->set_var('page', "$module_root/page/".($tp - 1)."/", 'BLOCK_article_page_prev');
		}
	}

	// if(!$art_id)
	$T->set_var('block_middle_class', 'light');

	$c = 0;
	foreach($articles as $item)
	{
		++$c;

		$item['res_date'] = date('d.m.Y', strtotime($item['res_entered']));

		if($item['table_id'] == Table::FORUM)
		{
			if($item['type_id']){
				$intro = mb_substr($item['res_data'], 0, 200);
				$intro = specialchars($intro);
				if(mb_strlen($item['res_data']) > 300){
					$intro .= "...";
				}
				$item['res_intro'] = $intro;
				$item['res_data'] = '';
			} else {
				$data_parts = preg_split("/<hr(\s+)?\/?>/", $item['res_data']);

				if(isset($data_parts[0]))
					$item['res_intro'] = $data_parts[0];

				if(isset($data_parts[1]))
				{
					$item['res_data'] = $data_parts[1];
				} else {
					$item['res_data'] = '';
				}
			}
			$item['res_route'] = Forum::Route((int)$item['doc_id'], $item['res_name']);
		} elseif($item['table_id'] == Table::ARTICLE){
			$item['res_route'] = Article::Route($item['module_id'], (int)$item['doc_id'], $item['res_name']);
		} else {
			throw new InvalidArgumentException("Unexpected table ID: $item[table_id]");
		}

		if($item['res_data'])
		{
			$T->enable('BLOCK_art_cont');
		} else {
			$T->disable('BLOCK_art_cont');
		}

		$T->enable_if(Res::hasNewComments($item), 'BLOCK_comments_new');

		# TODO: route
		// $T->set_var('res_route')
		$item['art_name_urlized'] = rawurlencode(urlize($item['res_name']));
		$T->set_array($item, 'BLOCK_article');

		# XXX: fix module_id
		if($item['table_id'] == Table::FORUM)
		{
			$T->set_var('module_id', "forum", 'BLOCK_article');
		} else {
			$T->set_var('module_id', $item['module_id'], 'BLOCK_article');
		}

		$T->parse_block('BLOCK_article', TMPL_APPEND);
	}

	return $T;
}

function update_profile(MainModule $template, array $data): bool
{
	global $sys_user_root, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

	$l_id = User::id();

	$OLD = Logins::load(new LoginsFilter(l_id: $l_id));

	if(empty($OLD))
	{
		$template->not_found('Konts nav atrasts vai ir neaktīvs!');
		return false;
	}

	$upd = new LoginsDummy();
	$upd->l_emailvisible = empty($data['l_emailvisible']) ? 0 : 1;
	$upd->l_disable_youtube = empty($data['l_disable_youtube']) ? 0 : 1;

	# TODO: constrain in DB?
	if(in_array($data['l_forumsort_themes'], [Forum::SORT_THEME, Forum::SORT_LASTCOMMENT])){
		$upd->l_forumsort_themes = $data['l_forumsort_themes'];
	}
	if(in_array($data['l_forumsort_msg'], [Forum::SORT_DESC, Forum::SORT_ASC])){
		$upd->l_forumsort_msg = $data['l_forumsort_msg'];
	}

	if(!(new LoginsEntity)->update($l_id, new LoginsType($upd)))
	{
		$template->not_found("Datubāzes kļūda");
		return false;
	}

	# TODO: db FS
	if(empty($_FILES['l_picfile']['tmp_name']))
	{
		return true;
	}

	$error_msg = [];
	$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
	$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';

	if(!save_upload('l_picfile', $save_path))
	{
		$template->error('Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']');
		return false;
	}

	if(!($type = image_load($in_img, $save_path)))
	{
		$error_msg[] = 'Nevar nolasīt failu ['.$_FILES['l_picfile']['name'].']';
		if(isset($GLOBALS['image_load_error']) && $GLOBALS['image_load_error']){
			$error_msg[] = " ($GLOBALS[image_load_error])";
		}
		$template->error($error_msg);
		return false;
	}

	list($w, $h, $type, $html) = getimagesize($save_path);

	Logins::delete_image();

	if($w > $user_pic_w || $h > $user_pic_h)
	{
		$out_img = image_resample($in_img, $user_pic_w, $user_pic_h);
		if(!image_save($out_img, $save_path, IMAGETYPE_JPEG))
		{
			$template->error('Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']');
			return false;
		}
	}

	if($w > $user_pic_tw || $h > $user_pic_th)
	{
		$out_img = image_resample($in_img, $user_pic_tw, $user_pic_th);
		if(!image_save($out_img, $tsave_path, IMAGETYPE_JPEG))
		{
			$template->error('Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']');
			return false;
		}
	}

	return true;
}

function prepare_res(
	int $res_resid = null,
	int $table_id,
	string $res_data,
	?string $res_name = null,
	?string $res_intro = null,
	?string $res_data_compiled = null,
	): ResType
{
	return ResType::initFrom(new ResDummy(
		res_resid: $res_resid,
		table_id: $table_id,
		login_id: User::id(),
		res_nickname: User::get_val('l_nick'),
		res_email: User::get_val('l_email'),
		res_ip: User::ip(),
		res_name: $res_name,
		res_intro: $res_intro,
		res_data: $res_data,
		res_data_compiled: $res_data_compiled ? $res_data_compiled : parse_text_data($res_data),
	));
}

# TODO: pēc migrēšanas uz Entity, te jau būs ?int
function format_vote(mixed $res_votes): string
{
	$res_votes = (int)$res_votes;

	if($res_votes > 0)
	{
		return "+$res_votes";
	} elseif($res_votes < 0) {
		return "$res_votes";
	} else {
		return "0";
	}
}

function comment_vote_class(mixed $res_votes): string
{
	$res_votes = (int)$res_votes;

	if($res_votes > 0)
	{
		return "vote-plus";
	} elseif($res_votes < 0) {
		return "vote-minus";
	} else {
		return "vote-zero";
	}
}
