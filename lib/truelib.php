<?php declare(strict_types = 1);

use dqdp\Template;
use dqdp\TemplateBlock;

function forum_add_theme(MainModule $template, Template $T, int $forum_id, array $data): bool
{
	global $db, $ip;

	if(!user_loged())
	{
		$template->not_logged();
		return false;
	}

	if(user_blacklisted())
	{
		$error_msg[] = "Blacklisted IP: $ip";
		return false;
	}

	$error_msg = $error_fields = [];
	if(empty($data['forum_name']))
	{
		$error_msg[] = "Nav norādīts tēmas nosaukums";
		$error_fields[] = 'forum_name';
	}

	if(empty($data['forum_data']))
	{
		$error_msg[] = "Nav norādīts ziņojums";
		$error_fields[] = 'forum_data';
	}

	# Tirgus
	if($forum_id == 107488){
		$params = [
			'get_votes'=>true,
			'get_comment_count'=>true,
			'l_id'=>$_SESSION['login']['l_id'],
		];
		$Logins = new Logins();
		$ldata = $Logins->load($params);

		$entered_days = (time() - strtotime($ldata['l_entered'])) / (3600 * 24);
		if(($entered_days < 10) || ($ldata['votes_plus'] - $ldata['votes_minus'] < 10)){
			$error_msg[] = 'Nepietiekams reitings. Jābūt vismaz 10 dienu vecam vai (plusi - mīnusi) vismaz 10';
		}
	}

	if($error_msg){
		$T->enable('BLOCK_forum_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	set_error_fields($T, $error_fields);

	$T->set_array(specialchars($data));

	if($error_msg){
		return false;
	}

	$forum = new Forum;
	$forum->validate($data);
	$data['login_id'] = $_SESSION['login']['l_id'];
	$data['forum_userlogin'] = $_SESSION['login']['l_login'];
	$data['forum_useremail'] = $_SESSION['login']['l_email'];
	$data['forum_username'] = $_SESSION['login']['l_nick'];
	$data['forum_allowchilds'] = Forum::PROHIBIT_CHILDS;

	$db->AutoCommit(false);
	$forum->setDb($db);
	if($id = $forum->add($forum_id, $data))
	{
		$newforum = new Forum;
		$new_data = $newforum->load(["forum_id"=>$id]);

		$res_id = (int)$new_data['res_id'];
		if(add_comment($db, $res_id, $data['forum_data']))
		{
			$db->Commit();
			header("Location: /forum/$id-".rawurlencode(urlize($data['forum_name'])));
			return true;
		}
	}
	$db->Rollback();
	$db->AutoCommit(true);

	return false;
}

function forum_themes(
	int $forum_id,
	array $forum_data,
	MainModule $template,
	string $action,
	int $fpp,
	int $page_id,
	int $pages_visible_to_sides,
): ?Template
{
	if(user_loged())
	{
		Forum::markThemeCount($forum_data);
	}

	$T = $template->add_file('forum/theme.tpl');

	$T->set_var('current_forum_id', $forum_id);
	$T->set_var('current_forum_name_urlized', rawurlencode(urlize($forum_data['forum_name'])));

	if($forum_id == 107488){
		$T->enable('BLOCK_forumdata_bazar');
	}

	$forum = new Forum;

	if($action == 'add_theme')
	{
		if(forum_add_theme($template, $T, $forum_id, post('data')))
		{
			return null;
		}
	}

	if(user_loged())
	{
		$T->enable('BLOCK_loggedin');
		$T->set_var('forum_username', $_SESSION['login']['l_nick'], 'BLOCK_loggedin');
	} else {
		$T->enable('BLOCK_notloggedin');
	}

	$forum_items = new Forum;
	$forum_items->setItemsPerPage($fpp);
	$forum_items->setPage($page_id);

	if(
		isset($_SESSION['login']['l_forumsort_themes']) &&
		($_SESSION['login']['l_forumsort_themes'] == Forum::SORT_LASTCOMMENT)
	)
	{
		$items = $forum_items->load(array(
			"forum_forumid"=>$forum_id,
			"order"=>"res_comment_lastdate DESC",
			));

		if($items)
			$T->enable('BLOCK_info_sort_C');
	} else {
		$items = $forum_items->load(array(
			"forum_forumid"=>$forum_id,
			));

		if($items)
			$T->enable('BLOCK_info_sort_T');
	}

	if($items)
	{
		$T->enable('BLOCK_forum_themes');
	} else {
		$T->enable('BLOCK_noforum');
	}

	$BLOCK_forum = $T->get_block('BLOCK_forum');
	foreach($items as $item)
	{
		$BLOCK_forum->enable_if(Forum::hasNewComments($item), 'BLOCK_comments_new');

		$BLOCK_forum->set_array($item);
		$BLOCK_forum->set_var('forum_name_urlized', rawurlencode(urlize($item['forum_name'])));
		$BLOCK_forum->set_var('forum_date', proc_date($item['forum_entered']));
		$BLOCK_forum->parse(TMPL_APPEND);
	}

	$forum_count = (int)$forum_data['forum_themecount'];
	forum_pages($page_id, $forum_count, $fpp, $pages_visible_to_sides, $T);

	$forum->set_forum_path($T, $forum_id);

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
	int $forum_id,
	array $forum_data,
	MainModule $template,
	string $action,
	string $hl,
): ?Template
{
	global $db;

	$res_id = (int)$forum_data['res_id']??0;

	$forum = new Forum;

	$T = $template->add_file('forum/det.tpl');
	$C = $template->add_file('comments.tpl');

	# Comments
	$params['order'] =
		isset($_SESSION['login']['l_forumsort_msg']) &&
		($_SESSION['login']['l_forumsort_msg'] == Forum::SORT_DESC)
		? "c_entered DESC"
		: "c_entered";

	$comments = get_res_comments($res_id, $params);

	# XXX : hack, vajag rādīt pa taisno foruma ierakstu
	if(($forum_data['forum_display'] == Forum::DISPLAY_DATA) && !empty($comments[0]))
	{
		# Ja sakārtots dilstoši, tad jāaiztiek ir pēdējais komments
		if(
			isset($_SESSION['login']['l_forumsort_msg']) &&
			($_SESSION['login']['l_forumsort_msg'] == Forum::SORT_DESC)
			)
		{
			array_unshift($comments, array_pop($comments));
		}
		$comments[0]['c_datacompiled'] = $forum_data['forum_data'];
	}

	comment_list($C, $comments, $hl);

	$forum->set_forum_path($T, $forum_id);

	# TODO: Vajag uztaisīt:
	# 1) lai rāda foruma datus
	# 2) uztaisīt balsošanu par articles un forum
	# 3) pārkopēt foruma pirmā komenta votes uz foruma votēm
	# 4) izvākt pirmo foruma komentu

	Res::markCommentCount($forum_data);

	if($forum_data['forum_closed'] == Forum::CLOSED)
	{
		$T->disable('BLOCK_addcomment');
		$T->enable('BLOCK_forum_closed');
	}

	$error_msg = [];
	if($action == 'add_comment'){
		if(!user_loged()){
			$template->not_logged();
			return null;
		}

		if($forum_data['forum_closed'] != Forum::OPEN)
		{
			$error_msg[] = "Tēma slēgta";
		}

		$data = post('data');
		$C->set_array(specialchars($data));

		if(empty($data['c_data'])){
			$error_msg[] = "Kaut kas jau jāieraksta";
		}

		if(!$error_msg) {
			if($c_id = add_comment($db, $res_id, $data['c_data']))
			{
				header("Location: ".Forum::Route($forum_data, $c_id));
				return null;
			} else {
				$error_msg[] = "Neizdevās pievienot komentāru";
			}
		}
	}

	if($error_msg) {
		$C->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	$T->set_block_string('BLOCK_forum_comments', $C->parse());

	# Attendees
	if(user_loged() && ($forum_data['type_id'] == Res::TYPE_EVENT))
	{
		$attended = false;
		$T->enable('BLOCK_attend');
		$T->set_var('res_id', $forum_data['res_id']);
		if($data = get_attendees((int)$forum_data['res_id']))
		{
			$T->enable('BLOCK_attend_list');
			$c = count($data);
			foreach($data as $k=>$item){
				if($item['a_attended'] && ($_SESSION['login']['l_id'] == $item['l_id'])){
					$attended = true;
				}
				$l_nick = $item['l_nick'];
				if(!$item['a_attended']){
					$l_nick = "<strike>$l_nick</strike>";
				}
				$l_nick .= ($k+1 < $c ? ', ' : '');
				$T->set_array($item, 'BLOCK_attend_list');
				$T->set_var('l_nick_', $l_nick, 'BLOCK_attend_list');
				$T->parse_block('BLOCK_attend_list', TMPL_APPEND);
			}
		}

		$ts = strtotime(date('d.m.Y', strtotime($forum_data['event_startdate']))) + 24 * 3600;
		if(time() < $ts){
			$T->enable('BLOCK_attend_'.($attended ? 'off' : 'on'));
		}
	}

	return $T;
}

function get_res_comments(int $res_id, array $params = [])
{
	$params = [ 'res_id'=>$res_id ];

	$RC = new ResComment();
	return $RC->Get($params);
}

function comment_list(
	Template $C,
	array $comments,
	string $hl
){
	if(user_loged())
	{
		$C->enable('BLOCK_addcomment');
		$C->set_var('c_username', $_SESSION['login']['l_nick']);
		$disabled_users = CommentDisabled::get($_SESSION['login']['l_id']);
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
		$item['res_votes'] = (int)$item['res_votes'];
		# balsošana
		if(user_loged() && $BLOCK_comment->block_exists('BLOCK_comment_vote')){
			$BLOCK_comment->enable('BLOCK_comment_vote');
		}

		if($item['res_votes'] > 0)
		{
			$BLOCK_comment->set_var('comment_vote_class', 'plus');
			$item['res_votes'] = '+'.$item['res_votes'];
		} elseif($item['res_votes'] < 0) {
			$BLOCK_comment->set_var('comment_vote_class', 'minus');
		} else {
			$BLOCK_comment->set_var('comment_vote_class', '');
		}

		if($hl){
			hl($item['c_datacompiled'], $hl);
		}

		$item['c_username'] = specialchars($item['c_username']);
		if(empty($disabled_users[$item['login_id']])){
			$BLOCK_comment->set_var('c_disabled_user_class', '');
		} else {
			$BLOCK_comment->set_var('c_disabled_user_class', ' disabled');
			$item['c_datacompiled'] = '-neredzams komentārs-';
		}

		$BLOCK_comment->set_array($item);
		$BLOCK_comment->set_var('c_date', proc_date($item['c_entered']));

		// Joined from logins
		if(user_loged() && ($item['l_login'] || $item['c_userlogin'] || $item['login_id'])){
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

		// if(user_loged() && ($item['c_userlogin'] || $item['login_id']))
		// 	$C->enable('BLOCK_profile_link');
		// else
		// 	$C->disable('BLOCK_profile_link');

		$BLOCK_comment->set_var('comment_nr', $comment_nr);

		$BLOCK_comment->parse(TMPL_APPEND);
	}
}

function get_attendees(int $res_id){
	global $db;

	$sql = "SELECT a.*, l.l_nick
	FROM attend a
	JOIN logins l ON l.l_id = a.l_id
	WHERE res_id = $res_id
	ORDER BY a_entered";

	return $db->Execute($sql);
}

// TODO: izvākt is_private
function set_profile(Template $T, $login, $is_private = false)
{
	global $sys_user_root;

	$login['l_forumsort_themes'] = isset($login['l_forumsort_themes']) ? $login['l_forumsort_themes'] : Forum::SORT_LASTCOMMENT;
	$login['l_forumsort_msg'] = isset($login['l_forumsort_msg']) ? $login['l_forumsort_msg'] : Forum::SORT_ASC;
	$pic_localpath = $sys_user_root.'/pic/'.$login['l_id'].'.jpg';
	$tpic_localpath = $sys_user_root.'/pic/thumb/'.$login['l_id'].'.jpg';

	$T->set_except(['l_password', 'l_sessiondata'], $login);

	$T->set_var('l_forumsort_themes_'.$login['l_forumsort_themes'], ' checked="checked"');
	$T->set_var('l_forumsort_msg_'.$login['l_forumsort_msg'], ' checked="checked"');

	if(!empty($login['l_disable_youtube']))
	{
		$T->set_var('l_disable_youtube_checked', ' checked="checked"');
	} else {
		$T->set_var('l_disable_youtube_checked', '');
	}

	if($login['l_emailvisible'] != Logins::EMAIL_VISIBLE)
	{
		$T->set_var('l_emailvisible', '');
	} else {
		$T->set_var('l_emailvisible', ' checked="checked"');
	}

	if(file_exists($pic_localpath) && file_exists($tpic_localpath))
	{
		$T->set_var('pic_path', "/user/thumb/$login[l_hash]/");
		if($info = getimagesize($pic_localpath))
		{
			$T->set_var('pic_w', $info[0]);
			$T->set_var('pic_h', $info[1]);
		} else {
			$T->set_var('pic_w', 400);
			$T->set_var('pic_h', 400);
		}

		$T->enable('BLOCK_picture');
		if($is_private){
			$T->enable('BLOCK_picture_del');
		}
	} else {
		$T->enable('BLOCK_nopicture');
	}

	$locale = "lv";
	$formatter = new IntlDateFormatter($locale, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);

	$T->set_var('l_entered_f', $formatter->format(strtotime($login['l_entered'])));
	$T->set_var('l_lastaccess_f', $formatter->format(strtotime($login['l_lastaccess'])));
	$days = floor((time() - strtotime($login['l_lastaccess'])) / (3600 * 24));
	if($days)
	{
		if($days < 365)
		{
			$days_lv = "dienām";
			if($days % 10 == 1)
				$days_lv = "dienas";
			$T->set_var('l_lastaccess_days', " (pirms $days $days_lv)");
		}
	} else {
		$T->set_var('l_lastaccess_days', " (šodien)");
	}
}

function public_profile(MainModule $template, string $l_hash): ?Template
{
	$action = post('action');

	if(!user_loged())
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
		($_SESSION['login']['l_id'] != $login_data['l_id'])
		)
	{
		if(isset($_POST['disable_comments']))
		{
			$ret = CommentDisabled::disable($_SESSION['login']['l_id'], $login_data['l_id']);
		} else {
			$ret = CommentDisabled::enable($_SESSION['login']['l_id'], $login_data['l_id']);
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

	if(CommentDisabled::get($_SESSION['login']['l_id'], $login_data['l_id']))
	{
		$T->set_var('disable_comments_checked', ' checked="checked"');
	} else {
		$T->set_var('disable_comments_checked', '');
	}

	if($_SESSION['login']['l_id'] != $login_data['l_id'])
	{
		$T->enable('BLOCK_disable_comments');
	}

	$template->set_title(" - $login_data[l_nick]");
	if($login_data['l_emailvisible'] == Logins::EMAIL_VISIBLE)
	{
		$T->enable('BLOCK_public_email');
	}

	set_profile($T, $login_data);

	return $T;
}

function private_profile(MainModule $template): ?Template
{
	global $db, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

	$module_root = "/user/profile";

	if(!user_loged())
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

	// save
	if(isset($_POST['data']))
	{
		$login = new Logins;
		$login_data = $_POST['data'];

		if($data = $login->update_profile($login_data))
		{
			unset($data['l_sessiondata']);
			$_SESSION['login'] = $data;
			header("Location: $module_root/");
			return null;
		} else {
			$template->error($login->error_msg);
		}
		$login_data = array_merge($_SESSION['login'], $login_data);
	} else {
		$login_data = $_SESSION['login'];
	} // post

	$T = $template->add_file('user/profile/private.tpl');

	$set_vars = array(
		'user_pic_w'=>$user_pic_w,
		'user_pic_h'=>$user_pic_h,
		'user_pic_tw'=>$user_pic_tw,
		'user_pic_th'=>$user_pic_th
	);

	$T->set_array($set_vars);

	set_profile($T, $login_data, true);

	# Comment stats
	# TODO: zem lib; active='Y'

	$ids = array();
	$data = array();
	for($r=0; $r<2; $r++)
	{
		if($r == 0){
			$v = 'r.res_votes_plus_count DESC';
		} elseif($r == 1){
			$v = 'r.res_votes_minus_count DESC';
		}

		$sql = "SELECT r.res_id FROM res r
		WHERE r.login_id = {$_SESSION['login']['l_id']}
		ORDER BY $v
		LIMIT 10";

		$ids[$r] = array_map(function($v) {
			return $v['res_id'];
		}, $db->Execute($sql));
		// $ids[$r] = $db->Execute($sql);
	}

	if($ids[0] || $ids[1]){
		$T->enable('BLOCK_truecomments');
	}

	foreach($ids as $k=>$i){
		if (!$i){
			continue;
		}
		if($k == 0){
			$order = "res_votes_plus_count DESC, res_votes_minus_count DESC";
			$T->set_var('truecomment_msg', 'Visvairāk plusotie komenti:');
		} elseif($k == 1){
			$order = "res_votes_minus_count DESC, res_votes_plus_count DESC";
			$T->set_var('truecomment_msg', 'Visvairāk mīnusotie komenti:');
		} else {
			assert(false, "unreachable");
		}

		$sql = "SELECT
			c.*,
			rc.res_id AS parent_res_id,
			r.res_votes_plus_count AS plus_count,
			r.res_votes_minus_count AS minus_count
		FROM
			comment c
		JOIN res r ON r.res_id = c.res_id
		JOIN res_comment rc ON rc.c_id = c.c_id
		WHERE
			r.res_id IN (".join(',', $i).")
		ORDER BY
			$order
		";

		$BLOCK_truecomment_item = $T->get_block('BLOCK_truecomment_item');

		$data = $db->Execute($sql);
		foreach($data as $item)
		{
			$plus_count = $item['plus_count'];
			$minus_count = $item['minus_count'];
			$c_data = $item['c_data'];
			if(mb_strlen($c_data) > 70){
				$c_data = mb_substr($c_data, 0, 70).'...';
			}

			$c_href = "/resroute/{$item['parent_res_id']}/?c_id={$item['c_id']}";

			$BLOCK_truecomment_item
			->set_var('minus_count', $minus_count)
			->set_var('plus_count', $plus_count)
			->set_var('c_data', $c_data)
			->set_var('c_href', $c_href)
			->parse(TMPL_APPEND);

			//$template->disable('BLOCK_truecomment_header');
		}
		$T->parse_block('BLOCK_truecomments', TMPL_APPEND);
	}

	// Passw status
	$sql = sprintf("SELECT bp.*
	FROM logins l
	JOIN bad_pass bp ON bp.pass_hash = l.l_password
	WHERE l.l_id = %d", $_SESSION['login']['l_id']);

	if($data = $db->ExecuteSingle($sql)){
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
	if(!user_loged())
	{
		$template->not_logged();
		return null;
	}

	$old_email = trim($_SESSION['login']['l_email']);

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

	$do_code = function(string $login, string $new_email, array &$error_msg): bool {
		global $sys_domain;

		$accept_code = Logins::insert_accept_code($login, $new_email);

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
			if(Logins::send_accept_code($login, $new_email, $subj, $msg))
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

	$result = !$error_msg && $do_code($_SESSION['login']['l_login'], $new_email, $error_msg);

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
	if(!user_loged())
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
		if(Logins::auth($_SESSION['login']['l_login'], $_POST['data']['old_password'])){
			if(!pw_validate($data['l_password'], $data['l_password2'], $error_msgs)){
				$error_fields[] = 'l_password';
			}
		} else {
			$error_msgs[] = 'Vecā parole nav pareiza';
			$error_fields[] = 'old_password';
		}
	}

	if(!$error_msgs){
		if((new Logins)->update_password($_SESSION['login']['l_login'], $data['l_password'])){
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
			$login_data = Logins::load_by_login($data['l_login']);
		} elseif($data['l_email']) {
			$login_data = Logins::load_by_email($data['l_email']);
		}

		if(empty($login_data))
		{
			$error_msg[] = 'Lietotājs netika atrasts vai ir bloķēts';
		}
	}

	if(empty($error_msg))
	{
		$l_login = $login_data['l_login'];
		if($forgot_code = Logins::insert_forgot_code($l_login)){
			try {
				if(Logins::send_forgot_code($l_login, $forgot_code, $login_data['l_email']))
				{
					$T->set_var('l_email', $login_data['l_email']);
					$T->enable('BLOCK_forgot_ok');
					$T->disable('BLOCK_forgot_form');
				} else {
					$error_msg[] = "Nevar nosūtīt kodu uz $login_data[l_email]";
				}
			} catch (Exception $e) {
				$error_msg[] = "Nevar nosūtīt kodu uz $login_data[l_email]<br>".$e->getMessage();
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

	$login_data = Logins::load_by_login($forgot_data['f_login']);

	if(empty($login_data))
	{
		$template->error('Lietotājs netika atrasts vai ir bloķēts');
		return $T;
	}

	$T->enable('BLOCK_forgot_pwch_form');
	$T->set_except(['l_password', 'l_sessiondata'], $login_data);

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$data = $_POST['data'];

	$error_msg = [];
	pw_validate($data['l_password']??"", $data['l_password2']??"", $error_msg);

	if(
		!$error_msg &&
		Logins::accept((int)$login_data['l_id']) &&
		Logins::remove_forgot_code($code) &&
		Logins::update_password($login_data['l_login'], $data['l_password'])
	) {
		header("Location: /forgot/accept/ok/");
		return null;
	}

	if($error_msg){
		$template->error($error_msg);
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

	$logins = new Logins;

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
		$error_msg[] = 'Nepareizs vai īss logins';
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

	if(!$error_msg && !$error_field) {
		if($logins->insert($data)){
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

function add_comment(SQLLayer $db, int $res_id, string $c_data)
{
	global $ip;

	// if(empty($c_data))
	// {
	// 	$error_msg[] = 'Nekorekti aizpildīta forma!';
	// 	// $template->enable('BLOCK_comment_error');
	// 	// $template->set_var('error_msg', 'Nekorekti aizpildīta forma!', 'BLOCK_comment_error');
	// 	return false;
	// }

	# Nočeko vai iepostēts tikai links
	// $url_pattern = url_pattern();
	// if(preg_match_all($url_pattern, $c_data, $matches))
	// {
	// 	foreach($matches[0] as $k=>$v)
	// 		$c_data = str_replace($matches[0][$k], '', $c_data);
	// }

	// $c_data = trim($c_data);

	// if(empty($c_data))
	// {
	// 	$error_msg[] = 'Pārāk pliks tas komentārs - links bez teksta!';
	// 	// $template->enable('BLOCK_comment_error');
	// 	// $template->set_var('error_msg', 'Pārāk pliks tas komentārs - links bez teksta!', 'BLOCK_comment_error');
	// 	return false;
	// }

	// if(user_blacklisted())
	// {
	// 	$error_msg["Blacklisted IP: $ip"];
	// 	// $template->enable('BLOCK_comment_error');
	// 	// $template->set_var('error_msg', "Blacklisted: $ip", 'BLOCK_comment_error');
	// 	return false;
	// }

	$cData = array(
		'login_id'=>$_SESSION['login']['l_id'],
		'c_userlogin'=>$_SESSION['login']['l_login'],
		'c_username'=>$_SESSION['login']['l_nick'],
		'c_useremail'=>$_SESSION['login']['l_email'],
		'c_data'=>$c_data,
		'c_userip'=>$ip,
		);

	$ResComment = new ResComment();
	$ResComment->setDb($db);

	return $ResComment->add($res_id, $cData);
}

function forum_root(MainModule $template): Template
{
	$forum_data = (new Forum())->load([
		"forum_forumid"=>0,
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
		$T->set_var('forum_name_urlized', rawurlencode(urlize($item['forum_name'])));
		$T->set_var('forum_date', proc_date($item['forum_entered']));
		$T->parse_block('BLOCK_forum', TMPL_APPEND);
	}

	return $T;
}

function user_image(string $l_hash, bool $thumb = false)
{
	global $sys_user_root, $sys_public_root;

	if(!user_loged()){
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
	$parts[] = "$login_data[l_id]$suffix.jpg";

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

function whatsnew(MainModule $template): Template
{
	$T = $template->add_file('whatsnew.tpl');

	if(!user_loged())
	{
		$template->not_logged();
		return false;
	}

	# Forum
	$data = (new Forum)->load([
		"order"=>'res_comment_lastdate DESC',
		"limit"=>50,
		"forum_allowchilds"=>Forum::PROHIBIT_CHILDS,
	]);

	if($data)
	{
		$R = new_template('forum/recent.tpl');
		foreach($data as $item)
		{
			$R->enable_if(Forum::hasNewComments($item), 'BLOCK_forum_r_comments_new');
			$R->set_var('forum_r_name', addslashes($item['forum_name']));
			$R->set_var('forum_r_comment_count', $item['res_comment_count']);
			$R->set_var('forum_r_path', "forum/{$item['forum_id']}-".rawurlencode(urlize($item["forum_name"])));
			$R->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
		}
		$T->set_block_string('BLOCK_whatsnew_forum', $R->parse());
	}

	# Articles
	$Article = new Article;
	$data = $Article->load([
		'order'=>'res_comment_lastdate DESC',
		'limit'=>50,
	]);

	if($data)
	{
		$R = new_template('right/comment_recent.tpl');
		foreach($data as $item)
		{
			$R->enable_if(Article::hasNewComments($item), 'BLOCK_comment_r_comments_new');

			$R->set_var('comment_r_name', $item['art_name']);
			$R->set_var('comment_r_comment_count', $item['res_comment_count']);
			$R->set_var('comment_r_path', "{$item['module_id']}/{$item['art_id']}-".urlize($item['art_name']));
			$R->parse_block('BLOCK_comment_r_items', TMPL_APPEND);
		}
		$T->set_block_string('BLOCK_whatsnew_comments', $R->parse());
	}

	return $T;
}
