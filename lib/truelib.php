<?php declare(strict_types = 1);

use dqdp\Template;
use dqdp\TODO;

function tm_shutdown()
{
	global $sys_debug;

	if($sys_debug)
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

function forum_add_theme(MainTemplate $template, ThemeEditFormTemplate $T, ViewResForumType $forum, array $post_data): bool
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

	$ignore_forum_name_strlen = !empty($post_data['ignore_forum_name_strlen']);

	$error_msg = $error_fields = [];
	if(empty($post_data['forum_name']))
	{
		$error_msg[] = "Nav norādīts tēmas nosaukums";
		$error_fields[] = 'forum_name';
	}

	if(!$ignore_forum_name_strlen && (strlen($post_data['forum_name']) > 255)){
		$error_msg[] = specialchars("Tēmas nosaukums par garu! Spied 'Pievienot', lai ignorētu");
		$error_fields[] = 'forum_name';
		$T->ignore_forum_name_strlen = true;
	}

	if(empty($post_data['forum_data']))
	{
		$error_msg[] = "Nav norādīts ziņojums";
		$error_fields[] = 'forum_data';
	}

	# Tirgus
	if($forum->forum_id == 107488){
		$entered_days = time() - strtotime(User::get_val('l_entered')) / (3600 * 24);
		if(($entered_days < 10) || (User::get_val('votes_plus') - User::get_val('votes_minus') < 10)){
			$error_msg[] = 'Nepietiekams reitings. Jābūt vismaz 10 dienu vecam vai (plusi - mīnusi) vismaz 10';
		}
	}

	if($error_msg){
		$T->error_msg = join("<br>", $error_msg);
	}

	// set_error_fields($T, $error_fields);
	$T->name = specialchars($post_data['forum_name']);
	$T->data = specialchars($post_data['forum_data']);

	if($error_msg){
		return false;
	}

	$R = Res::prepare_with_user(
		res_resid: $forum->res_id,
		res_kind: ResKind::FORUM,
		res_name: $post_data['forum_name'],
		res_data: $post_data['forum_data'],
	);

	return DB::withNewTrans(function() use ($R){
		if($res_id = $R->insert()){
			if($forum_id = (new ForumType(
				res_id: $res_id,
				forum_allow_childs: 0
			))->insert()){
				$new = ViewResForumEntity::get_by_id($forum_id);
				$U = new ResType(res_id:$res_id, res_route:$new->Route());
				if($U->update()){
					header("Location: $U->res_route");
					return true;
				}
			}
		}

		return false;
	});
}

function forum_themes(
	MainTemplate $template,
	ViewResForumType $forum,
	string $action,
	int $items_per_page,
	int $page_id,
	int $pages_visible_to_sides,
): ?ForumThemeListTemplate
{
	Res::mark_as_seen($forum->res_id);

	$T = new ForumThemeListTemplate;
	$T->is_logged = User::logged();
	$T->pages_visible_to_sides = $pages_visible_to_sides;
	$T->items_per_page = $items_per_page;
	$T->page_id = $page_id;
	set_res($T, $forum);

	$Filter = (new ResForumFilter(res_resid: $forum->res_id))->page($page_id, $items_per_page);
	if(User::get_val('l_forumsort_themes') == Forum::SORT_LASTCOMMENT)
	{
		$Filter->orderBy("COALESCE(res_comment_last_date, res_entered) DESC");
		$T->is_sorted_C = true;
	} else {
		$Filter->orderBy("res_entered DESC");
		$T->is_sorted_T = true;
	}

	$T->themes = (new ViewResForumEntity)->get_all($Filter);
	$T->form = new ThemeEditFormTemplate;
	if($T->is_logged){
		$T->form->nick_name = User::nick();
	}

	if($forum->forum_id == 107488){
		$T->is_bazaar= true;
	}

	if($action == 'add_theme')
	{
		if(forum_add_theme($template, $T->form, $forum, post('data')))
		{
			return null;
		}
	}

	return $T;
}

function set_res(AbstractResTemplate $T, ViewResType&ResourceTypeInterface $res, string $hl = null)
{
	$T->res_id = $res->res_id;
	$T->res_date = proc_date($res->res_entered);
	$T->res_date_short = proc_date_short($res->res_entered);
	$T->res_votes = format_vote($res->res_votes);
	$T->comment_vote_class = comment_vote_class($res->res_votes);
	$T->res_route = $res->res_route;
	$T->res_name = specialchars($res->res_name);
	$T->res_intro = $res->res_intro;
	$T->res_data = $res->res_data;
	$T->res_data_compiled = $res->res_data_compiled;
	$T->res_nickname = specialchars($res->res_nickname);
	$T->res_votes_plus_count = $res->res_votes_plus_count;
	$T->res_votes_minus_count = $res->res_votes_minus_count;
	$T->l_hash = $res->l_hash;
	$T->res_child_count = $res->res_child_count;
	$T->res_comment_count = $res->res_comment_count;
	$T->res_comment_last_date = $res->res_comment_last_date;

	if($res->res_name && $hl){
		$T->res_name = hl($res->res_name, $hl);
	}
	if($res->res_intro && $hl){
		$T->res_intro = hl($res->res_intro, $hl);
	}
	if($res->res_data && $hl){
		$T->res_data = hl($res->res_data, $hl);
	}
	if($res->res_data_compiled && $hl){
		$T->res_data_compiled = hl($res->res_data_compiled, $hl);
	}

	$T->vote_control_enabled = User::logged();
	$T->profile_link_enabled = User::logged() && $res->l_hash;
	$T->can_edit_res = User::can_edit_res($res);
	$T->can_debug_res = User::can_debug_res($res);
}

function forum_det(
	MainTemplate $template,
	ViewResForumType $forum,
	string $action,
	string $hl,
): ?ForumDetTemplate
{
	$T = new ForumDetTemplate;

	set_res($T, $forum, $hl);

	$res_data = $forum->res_data_compiled;
	if($forum->forum_display == Forum::DISPLAY_DATA){
		$res_data = $forum->res_data;
	}

	if($res_data && $hl) {
		$res_data = hl($res_data, $hl);
	}

	$T->res_data_compiled = $res_data;

	# Comments
	if(User::get_val('l_forumsort_msg') == Forum::SORT_DESC){
		$T->is_sorted_D = true;
	} else {
		$T->is_sorted_A = true;
	}

	// $forum->set_forum_path($T, $forum_id);

	Res::mark_as_seen($forum->res_id);

	if($forum->forum_closed)
	{
		$T->is_closed = true;
	} else {
		$T->CommentFormT = new CommentAddFormTemplate;
		$T->CommentFormT->is_logged = User::logged();
		if($T->CommentFormT->is_logged) {
			$T->CommentFormT->l_nick = specialchars(User::data()->l_nick);
		}

		if($action == 'add_comment')
		{
			$error_msg = [];
			if(add_comment($template, $forum->res_id, post('res_data'), $error_msg)){
				return null;
			} else {
				$T->CommentFormT->res_data = specialchars(post('res_data'));
				$T->CommentFormT->error_msg = join("<br>", $error_msg);
			}
		}
	}

	$Filter = (new ResFilter())->orderBy(User::get_val('l_forumsort_msg') == Forum::SORT_DESC ? "res_entered DESC" : "res_entered");
	$T->CommentListT = new CommentsListTemplate;
	$T->CommentListT->Comments = Res::get_comments($forum->res_id, $Filter);
	$T->CommentListT->hl = $hl;

	# Attendees
	if(User::logged() && ($forum->type_id === Forum::TYPE_EVENT))
	{
		$T->AttendT = attendees_view($forum);
	}

	return $T;
}

function public_profile(MainModule $template, string $l_hash): ?Template
{
	$action = post('action');

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($L = Logins::load_by_login_hash($l_hash, true))){
		$template->not_found();
		return null;
	}

	$T = $template->add_file('user/profile/user.tpl');

	if(!$L->l_active){
		$T->set_var('is_blocked', ' (bloķēts)');
	}

	# Disable comments
	if(
		($action == 'disable_comments') &&
		(User::id() != $L->l_id)
		)
	{
		if(isset($_POST['disable_comments']))
		{
			$ret = CommentDisabled::disable(User::id(), $L->l_id);
		} else {
			$ret = CommentDisabled::enable(User::id(), $L->l_id);
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

	if(CommentDisabled::get(User::id(), $L->l_id))
	{
		$T->set_var('disable_comments_1', ' checked="checked"');
	}

	if(User::id() != $L->l_id)
	{
		$T->enable('BLOCK_disable_comments');
	}

	$template->set_title(" - $L->l_nick");
	if($L->l_emailvisible)
	{
		$T->enable('BLOCK_public_email');
	} else {
		$T->enable('BLOCK_public_email_invisible');
	}

	$T->set_except(['l_password', 'l_sessiondata'], $L);

	if(user_thumb_exists($L->l_id) && user_image_exists($L->l_id))
	{
		$T->set_var('thumb_path', "/user/thumb/$L->l_hash/");
		$T->enable('BLOCK_picture');
	} else {
		$T->enable('BLOCK_nopicture');
	}

	$formatter = new IntlDateFormatter("lv", IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
	$days = floor((time() - strtotime($L->l_lastaccess)) / (3600 * 24));

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
		$l_lastaccess_f = $formatter->format(strtotime($L->l_lastaccess));
	}

	$T->set_var('l_lastaccess_f', $l_lastaccess_f);
	$T->set_var('l_entered_f', $formatter->format(strtotime($L->l_entered)));

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
		$L = LoginsType::initFromDirty($post_data, User::data());
	} else {
		$L = LoginsType::initFrom(User::data());
	}

	$T = $template->add_file('user/profile/private.tpl');

	# TODO: get rid off, kad implementēs imager
	$set_vars = array(
		'user_pic_w'=>$user_pic_w,
		'user_pic_h'=>$user_pic_h,
		'user_pic_tw'=>$user_pic_tw,
		'user_pic_th'=>$user_pic_th
	);

	$T->set_array($set_vars);

	$T->set_var("l_forumsort_themes_$L->l_forumsort_themes", ' checked="checked"');
	$T->set_var("l_forumsort_msg_$L->l_forumsort_msg", ' checked="checked"');
	$T->set_var("l_disable_youtube_$L->l_disable_youtube", ' checked="checked"');
	$T->set_var("l_emailvisible_$L->l_emailvisible", ' checked="checked"');

	$T->set_except(['l_password', 'l_sessiondata'], $L);

	if(user_thumb_exists($L->l_id) && user_image_exists($L->l_id))
	{
		$T->set_var('thumb_path', "/user/thumb/$L->l_hash/");
		$T->enable('BLOCK_picture');
		$T->enable('BLOCK_picture_del');
	} else {
		$T->enable('BLOCK_nopicture');
	}

	$F = (new ResCommentFilter(
		login_id:User::id()
	))->rows(10);

	$top_comments[0] = (new ViewResEntity)->getAll($F->orderBy('res_votes_plus_count DESC'));
	$top_comments[1] = (new ViewResEntity)->getAll($F->orderBy('res_votes_minus_count DESC'));

	if(count($top_comments[0]) || count($top_comments[1])){
		$T->enable('BLOCK_truecomments');
	}

	foreach($top_comments as $r=>$data)
	{
		if (!$data->count()){
			continue;
		}

		if($r == 0){
			$T->set_var('truecomment_msg', 'Visvairāk plusotie ieraksti:');
		} elseif($r == 1){
			$T->set_var('truecomment_msg', 'Visvairāk mīnusotie ieraksti:');
		} else {
			assert(false, "unreachable");
		}

		$BLOCK_truecomment_item = $T->get_block('BLOCK_truecomment_item');
		foreach($data as $item)
		{
			$res_data = $item->res_data;
			if(mb_strlen($res_data) > 70){
				$res_data = mb_substr($res_data, 0, 70).'...';
			}

			$BLOCK_truecomment_item
			->set_var('res_votes_plus_count', $item->res_votes_plus_count)
			->set_var('res_votes_minus_count', $item->res_votes_minus_count)
			->set_var('res_data', specialchars($res_data))
			->set_var('res_href', $item->res_route)
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
		$t->set_var('code_timeout', round(Logins::codes_timeout() / 60));
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
		if(Logins::auth(User::email(), $_POST['data']['old_password'])){
			if(!pw_validate($data['l_password'], $data['l_password2'], $error_msgs)){
				$error_fields[] = 'l_password';
			}
		} else {
			$error_msgs[] = 'Vecā parole nav pareiza';
			$error_fields[] = 'old_password';
		}
	}

	if(!$error_msgs){
		if((new Logins)->update_password(User::email(), $data['l_password'])){
			$template->msg("Parole nomainīta.");
			return null;
		} else {
			$error_msgs[] = "Datubāzes kļūda";
		}
	}

	if($error_msgs){
		set_error_fields($T, $error_msgs);
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
		if($data['l_email']) {
			$L = Logins::load_by_email($data['l_email']);
		} elseif($data['l_login']){
			$L = Logins::load_by_login($data['l_login']);
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
				Logins::update_password($L->l_email, $data['l_password'])
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

# TODO: pārbaudīt lauku garumus
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

	$T->set_var('exp1', rand(0, 100000));
	$T->set_var('exp2', rand(0, 100000));

	if(!isset($_POST['data']))
	{
		return $T;
	}

	$check = ['l_nick', 'l_password', 'l_email' ];

	$data = $_POST['data'];

	$exp1 = (int)post('exp1');
	$exp2 = (int)post('exp2');
	$exp_val = post('exp_val');

	$error_msg = $error_field = [];
	foreach($check as $c) {
		$data[$c] = isset($data[$c]) ? trim($data[$c]) : '';
		if(empty($data[$c])){
			$error_field[] = $c;
		}
	}

	if($exp1 + $exp2 != $exp_val){
		$error_msg[] = 'Spam check fail';
	}

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

	if(!is_valid_email($data['l_email'])) {
		$error_msg[] = 'Nekorekta e-pasta adrese';
		$error_field[] = 'l_email';
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
				email($sys_mail, '[truemetal] jauns lietotajs', "$data[l_email] ($data[l_nick])\n\nIP:$_SERVER[REMOTE_ADDR]");
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

// function set_error_fields(TemplateBlock $T, array $fields){
// 	foreach($fields as $k)
// 	{
// 		$T->set_var('error_'.$k, ' class="error-form"');
// 	}
// }

function forum_root(): ForumRootListTemplate
{
	$F = (new ResForumFilter(res_resid: false))->orderBy("forum_id ASC");

	$T = new ForumRootListTemplate;
	$T->forums = (new ViewResForumEntity)->get_all($F);

	return $T;
}

function user_image(string $l_hash, bool $thumb = false, string $suffix = "")
{
	global $sys_user_root, $sys_public_root;

	if(!User::logged()){
		header403();
		return;
	}

	$suffix = $suffix ? "-$suffix" : "";

	$login_data = Logins::load_by_login_hash($l_hash);
	if(empty($login_data)){
		header404();
		return;
	}

	$parts = [$sys_user_root, "pic"];
	if($thumb)$parts[] = "thumb";
	$parts[] = "$login_data->l_id$suffix.jpg";

	$pic_localpath = join(DIRECTORY_SEPARATOR, $parts);

	if(file_exists($pic_localpath) && ($info = getimagesize($pic_localpath)))
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

function get_whatsnew_template(MainTemplate $template): ?WhatsnewTemplate
{
	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	$W = new WhatsnewTemplate;

	# Forum
	$F = (new ResForumFilter(forum_allow_childs: 0))->rows(50)->orderBy('res_comment_last_date DESC');
	$T = new CommentsRecentTemplate;
	$T->data = (new ViewResForumEntity)->get_all($F);
	$T->show_more = false;
	$W->ForumRecent = $T;

	# Articles
	$F = (new ResArticleFilter)->rows(50)->orderBy('res_comment_last_date DESC');
	$T = new CommentsRecentTemplate;
	$T->data = (new ViewResArticleEntity)->get_all($F);
	$T->show_more = false;
	$W->CommentsRecent = $T;

	return $W;
}

function user_comments(MainModule $template, string $l_hash, string $hl): ?Template
{
	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	if(!($login_data = Logins::load_by_login_hash($l_hash, true)))
	{
		$template->not_found();
		return null;
	}

	$T = $template->add_file('user/comments.tpl');
	$T->set_var('l_nick', $login_data->l_nick);
	if(!$login_data->l_active){
		$T->set_var('is_blocked', ' (bloķēts)');
	}

	$F = (new ResCommentFilter(login_id: $login_data->l_id))->rows(100)->orderBy("res_entered DESC");
	$comments = (new ViewResCommentEntity)->getAll($F);

	$T->set_var('user_comments', comment_list($comments, $hl)->parse());

	return $T;
}

function gallery_thumbs_list(MainModule $template, int $gal_id): ?Template
{
	global $CACHE_ENABLE;

	// $F = (new ResGalleryFilter(gal_id: $gal_id))->orderBy("res_entered DESC, gg_date DESC");

	$gal = (new ViewResGalleryEntity)->get_by_id($gal_id);

	if(!$gal){
		$template->not_found();
		return null;
	}

	// $GD = new GalleryData;
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
	if($gal->gal_ggid)
		$gal_name .= "$gal->gg_name / ";
	$gal_name .= "$gal->res_name";

	$T->set_var('gal_name', $gal_name);
	$T->set_var('gal_id', $gal->gal_id);
	$template->set_title('Galerija '.$gal_name);

	if($gal->gal_ggid){
		$T->set_var('gal_jump_id', "gg_".$gal->gal_ggid);
	} else {
		$T->set_var('gal_jump_id', "gal_".$gal->gal_id);
	}

	$T->enable('BLOCK_thumb_list');

	# ielasam thumbus
	$tpr = 5;
	$c = 0;

	$F = (new ResGdFilter(res_resid: $gal->res_id))->orderBy("res_name");

	$data = (new ViewResGdEntity)->getAll($F);

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

		$T->set_array($thumb, 'BLOCK_thumb');

		if($CACHE_ENABLE && ($hash = cache_hash($thumb->gd_id."thumb.jpg")) && cache_exists($hash)){
			$T->set_var('thumb_path', cache_http_path($hash), 'BLOCK_thumb');
		} else {
			$T->set_var('thumb_path', "/gallery/thumb/$thumb->gd_id/", 'BLOCK_thumb');
		}

		$T->set_var('res_votes', format_vote($thumb->res_votes));
		$T->set_var('comment_class', GalleryData::has_new_comments($thumb) ? "Comment-count-new" : "Comment-count-old");

		$T->parse_block('BLOCK_thumb', TMPL_APPEND);
	}

	return $T;
}

function gallery_root(MainModule $template): ?Template
{
	$F = (new ResGalleryFilter(res_resid: false))->orderBy("res_entered DESC, gg_date DESC");

	$data = (new ViewResGalleryEntity)->getAll($F);

	if(!$data)
	{
		$template->not_found();
		return null;
	}

	$T = $template->add_file('gallery.tpl');

	$T->enable('BLOCK_gallery_list');

	$data2 = array();
	foreach($data as $gal) {
		$k = empty($gal->gal_ggid) ? "e-".$gal->gal_id : $gal->gal_ggid;
		$data2[$k][] = $gal;
	}

	foreach($data2 as $data)
	{
		$T->set_array($data[0], 'BLOCK_gallery_list');
		if($data[0]->gal_ggid){
			$T->set_var('gg_name', $data[0]->gg_name, 'BLOCK_gallery_group');
			$T->set_var('gal_jump_id', "gg_".$data[0]->gg_id, 'BLOCK_gallery_group');
		} else {
			$T->set_var('gg_name', $data[0]->res_name, 'BLOCK_gallery_group');
			$T->set_var('gal_jump_id', "gal_".$data[0]->gal_id, 'BLOCK_gallery_group');
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

	if($CACHE_ENABLE){
		$hash = cache_hash($gd_id.$gal_type.".jpg");
	}

	if($CACHE_ENABLE && cache_exists($hash)){
		$jpeg = cache_read($hash);
	} else {
		// $data = $GD->load(['gd_id'=>$gd_id, 'load_images'=>true]);
		$data = ViewResGdDataEntity::get_by_id($gd_id);
		$jpeg = $gal_type == 'image' ? $data->gd_data : $data->gd_thumb;

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

	// $GD = new GalleryData;

	if(!($galdata = ViewResGdEntity::get_by_id($gd_id))){
		$template->not_found();
		return null;
	}

	// $gallery = new Gallery;
	// $gal = $gallery->load($galdata->gal_id);
	$gal = ViewResGalleryEntity::get_by_res_id($galdata->res_resid);

	Res::mark_as_seen($galdata->res_id);

	$T = $template->add_file('gallery.tpl');

	$F = (new ResFilter())->orderBy(User::get_val('l_forumsort_msg') == Forum::SORT_DESC ? "res_entered DESC" : "res_entered");
	$comments = Res::get_comments($galdata->res_id, $F);

	$C = comment_list($comments, "");

	$error_msg = [];
	if($action == 'add_comment')
	{
		if(add_comment($template, $C, $galdata->res_id, post('res_data'), $error_msg)){
			return null;
		}
	}

	$F = comment_add_form();
	if($error_msg)
	{
		$F->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	$T->set_var('gallery_comments', $C->parse());
	$T->set_var('comment_add_form', $F->parse());

	# ja skataas pa vienai
	$T->enable('BLOCK_image');

	if($CACHE_ENABLE && ($hash = cache_hash($gd_id."image.jpg")) && cache_exists($hash)){
		$T->set_var('image_path', cache_http_path($hash), 'BLOCK_image');
	} else {
		$T->set_var('image_path', "/gallery/image/$gd_id/", 'BLOCK_image');
	}

	# nechekojam, vai ir veel bildes
	$next_id = GalleryData::get_next_data($gal->res_id, $gd_id);
	$T->set_var('gd_nextid', $next_id ? $next_id : $gd_id, 'BLOCK_image');

	$T->set_array($galdata);
	$T->set_var('gal_jump_id', "gg_".$gal->gal_ggid);
	$T->set_var('gal_id', $gal->gal_id);

	if(User::logged() && $galdata->l_hash){
		$T->set_var('l_hash', $galdata->l_hash);
		$T->enable('BLOCK_profile_link');
	} else {
		$T->disable('BLOCK_profile_link');
	}

	if(User::logged()){
		$T->enable('BLOCK_comment_vote');
	}

	$T->set_var('res_date', proc_date($galdata->res_entered));
	$T->set_var('res_votes', format_vote($galdata->res_votes));
	$T->set_var('comment_vote_class', comment_vote_class($galdata->res_votes));

	return $T;
}

function admin_comment_list(
	Template $C,
	ViewResCommentCollection $comments
){

	if($comments->count())
	{
		$C->enable('BLOCK_comments');
	} else {
		$C->enable('BLOCK_no_comments');
	}

	foreach($comments as $item)
	{
		$C->set_array($item, 'BLOCK_comment_item');

		$C->set_var('c_origin_href', $item->res_route);
		$C->set_var('c_origin_name', "#comment$item->c_id");

		if($item->res_visible)
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

	if(!($res = ResEntity::get($res_id))){
		$template->not_found();
		return null;
	}

	if(User::id() == $res->login_id)
	{
		$template->msg(specialchars($value == 'up' ? ":)" : ">:)"));
		return null;
	}

	# Check count 24h
	$countCheck = DB::ExecuteSingle(
		"SELECT COUNT(*) cc FROM res_vote WHERE login_id = ? AND rv_entered > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)",
		User::id()
	);

	if(!$countCheck)
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($countCheck['cc'] > 24)
	{
		$template->msg("Pārsniegtiņš divdesmitčetriņu stundiņu limitiņš balsošaniņai.");
		return null;
	}

	$timeout = 5;
	$sql = "INSERT INTO res_vote (
		res_id, login_id, rv_value, rv_userip
	) VALUES (
		?, ?, ?, ?
	) ON DUPLICATE KEY UPDATE
		rv_value = CASE WHEN TIMESTAMPDIFF(MINUTE, rv_entered, CURRENT_TIMESTAMP) < $timeout THEN
			CASE WHEN VALUES(rv_value) <=> rv_value THEN 0 ELSE VALUES(rv_value) END
		ELSE rv_value END,
		rv_userip = CASE WHEN TIMESTAMPDIFF(MINUTE, rv_entered, CURRENT_TIMESTAMP) < $timeout THEN VALUES(rv_userip) ELSE rv_userip END
	";

	$inserted = DB::Execute(
		$sql,
		$res->res_id,
		User::id(),
		$value == 'up' ? 1 : -1,
		$ip
	);

	if(!$inserted || !($new_data = ViewResEntity::get_by_id($res_id)))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($json){
		$retJson = new StdClass;
		$retJson->Votes = $new_data->res_votes;
		return new JsonResponse($retJson);
	} else {
		redirect_referer();
		return null;
	}
}

function attend(MainTemplate $template, int $res_id, ?string $off = null): ?TrueResponseInterface
{
	$json = isset($_GET['json']);

	if(!User::logged())
	{
		$template->not_logged();
		return null;
	}

	# TODO: kādreiz atdalīt pasākumus savā klasē
	if(!($item = ViewResForumEntity::get_by_res_id($res_id)))
	{
		$template->not_found();
		return null;
	}

	if($item->type_id !== Forum::TYPE_EVENT)
	{
		$template->forbidden("Nav pasākums");
		return null;
	}

	if(time() > (strtotime(date('d.m.Y', strtotime($item->event_startdate))) + 24 * 3600)){
		$template->msg("Par vēlu");
		return null;
	}

	if(!AttendEntity::attend(User::id(), $res_id, $off == 'off' ? 0 : 1))
	{
		$template->error("Datubāzes kļūda");
		return null;
	}

	if($json){
		return new JsonResponse(["OK"=>true]);
	} else {
		header("Location: $item->res_route");
		return null;
	}
}

function attendees_view(ViewResForumType $forum): AttendTemplate
{
	$T = new AttendTemplate;
	$T->l_id = User::id();
	$T->res_id = $forum->res_id;
	$T->attendees = ViewAttendEntity::get_by_res_id($forum->res_id);
	$T->event_startdate = $forum->event_startdate;

	return $T;
}

function archive(MainModule $template): ?Template
{
	$T = $template->add_file('archive.tpl');

	$q = DB::Query("SELECT * FROM view_mainpage ORDER BY res_entered DESC");

	$old_date = '';
	// $formatter = new IntlDateFormatter("lv", IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
	$menesi = menesi();

	$count = 0;
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
		$T->set_var('res_route', $item['res_route']);
		$T->parse_block('BLOCK_archive_items', TMPL_APPEND);
		$count++;
	}

	if($count){
		$T->enable('BLOCK_archive_items');
	} else {
		$T->enable('BLOCK_no_archive');
	}

	return $T;
}

function article(MainTemplate $template, int $art_id, string $hl, ?string $article_route = null): ?ArticleTemplate
{
	if(!($art = ViewResArticleEntity::get_by_id($art_id))){
		$template->not_found();
		return null;
	}

	if($article_route && !str_ends_with($art->res_route, "/$article_route"))
	{
		redirectp_wqs($art->res_route);
		return null;
	}

	Res::mark_as_seen($art->res_id);

	$T = new ArticleTemplate;
	set_res($T, $art, $hl);

	$T->art_id = $art_id;

	$T->CommentListT = new CommentsListTemplate;
	$T->CommentListT->Comments = Res::get_comments($art->res_id);
	$T->hl = $hl;

	$T->CommentFormT = new CommentAddFormTemplate;
	$T->CommentFormT->is_logged = User::logged();
	if($T->CommentFormT->is_logged) {
		$T->CommentFormT->l_nick = specialchars(User::data()->l_nick);
	}

	$error_msg = [];
	$action = post('action');

	if($action == 'add_comment')
	{
		$error_msg = [];
		if(add_comment($template, $art->res_id, post('res_data'), $error_msg)){
			return null;
		} else {
			$T->CommentFormT->res_data = specialchars(post('res_data'));
			$T->CommentFormT->error_msg = join("<br>", $error_msg);
	}
	}

	$template->set_title($art->res_name);

	return $T;
}

function mainpage(MainTemplate $template, int $page, int $items_per_page): ArticleListTemplate
{
	global $sys_module_id, $module_root;

	$T = new ArticleListTemplate;

	# TODO: cache, meta tabulā varbūt? view_mainpage vispār vajadzētu pārģenerēt tikai pēc vajadzības
	// $sql = (new Select('COUNT(*) AS cc'))->From('view_mainpage');

	// $cc = DB::ExecuteSingle($sql);
	// $tc = (int)$cc['cc'];

	// if($sys_module_id != 'article'){
	// 	$sql->Where(['module_id = ?', $sys_module_id]);
	// }

	$F = (new ViewMainpageFilter)->rows($items_per_page);

	$A = new ViewMainpageEntity;
	$tc = $A->count($F);

	$tp = (int)ceil($tc / $items_per_page);
	$art_align = $tc % $items_per_page;

	if(($page < 0) || ($page >= $tp))
	{
		header("Location: $module_root/");
		return null;
	}

	if($page){
		$limit = (($tp - $page - 1) * $items_per_page + $art_align);
		$F->Offset($limit);
			}

	$T->articles = $A->get_all($F);
	$T->total_count = $tc;
	$T->total_pages = $tp;
	$T->items_per_page = $items_per_page;
	$T->current_page = $page ? $page : $tp;
	$T->module_root = $module_root;

	return $T;
}

function update_profile(MainModule $template, array $data): bool
{
	global $sys_user_root, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

	$l_id = User::id();

	$OLD = Logins::load_by_id($l_id);

	if(empty($OLD))
	{
		$template->not_found('Konts nav atrasts vai ir neaktīvs!');
		return false;
	}

	$upd = new LoginsType(
		l_id: $l_id,
		l_emailvisible: empty($data['l_emailvisible']) ? 0 : 1,
		l_disable_youtube: empty($data['l_disable_youtube']) ? 0 : 1,
	);

	if(in_array($data['l_forumsort_themes'], [Forum::SORT_THEME, Forum::SORT_LASTCOMMENT])){
		$upd->l_forumsort_themes = (int)$data['l_forumsort_themes'];
	}
	if(in_array($data['l_forumsort_msg'], [Forum::SORT_DESC, Forum::SORT_ASC])){
		$upd->l_forumsort_msg = (int)$data['l_forumsort_msg'];
	}

	if(!$upd->update())
	{
		$template->error("Datubāzes kļūda");
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

function format_vote(?int $res_votes): string
{
	if(empty($res_votes)){
		return "0";
	} elseif($res_votes > 0){
		return "+$res_votes";
	} elseif($res_votes < 0) {
		return "$res_votes";
	}
}

function comment_vote_class(?int $res_votes): string
{
	if(empty($res_votes)){
		return "vote-zero";
	} elseif($res_votes > 0){
		return "vote-plus";
	} elseif($res_votes < 0) {
		return "vote-minus";
	}
}

function load_specific_res(int $res_id, int $res_kind): ?ResourceTypeInterface
{
	switch($res_kind)
	{
		case ResKind::ARTICLE:
			return ViewResArticleEntity::get_by_res_id($res_id);
		case ResKind::FORUM:
			return ViewResForumEntity::get_by_res_id($res_id);
		case ResKind::COMMENT:
			return ViewResCommentEntity::get_by_res_id($res_id);
		case ResKind::GALLERY:
			new TODO("Get Gallery");
		case ResKind::GALLERY_DATA:
			new TODO("Get GalleryData");
	}

	throw new InvalidArgumentException("Table unknown: $res_kind");
}

function load_res(int $res_id): ?ResourceTypeInterface
{
	if($res = ResEntity::get($res_id))
	{
		return load_specific_res($res->res_id, $res->res_kind);
	}

	return null;
}

function get_res_tree(?int $res_id = null, ?int $res_kind = null): ?array
{
	if(is_null($res_id)){
		return null;
	}

	$f = $res_kind ? load_specific_res($res_id, $res_kind) : load_res($res_id);

	if($f){
		return [$f, get_res_tree($f->res_resid, $res_kind)];
	}

	return null;
}

function tm_search(SearchParams $params)
{
	require_once("lib/sphinxapi.php");

	# Sphinx
	$spx = new SphinxClient();
	$spx->_mode = SPH_MATCH_BOOLEAN;
	if($params->limit){
		$spx->SetLimits(0, $params->limit);
	}
	$spx->SetConnectTimeout(4);
	$spx->SetServer('127.0.0.1', 3313);

	$spx->SetSortMode(SPH_SORT_ATTR_DESC, "doc_comment_last_date");
	// $spx->SetSortMode(SPH_SORT_RELEVANCE);

	if($params->filters){
		foreach($params->filters as $k=>$v){
			$spx->SetFilter($k, $v);
		}
	}

	return [
		// $spx->Query($spx->EscapeString($params->q), $params->index),
		$spx->Query($params->q, $params->index),
		$spx
	];
}


function search(MainModule $template, array $DOC_SOURCES, array &$err_msg)
{
	$spx_limit = 250;

	if(request_method() == "POST"){
		$only_titles       =  (bool)post('only_titles');
		$include_comments  =  (bool)post('include_comments');
		$checked_sources   =  post('sources', []);
	} else {
		$only_titles       =  false;
		$include_comments  =  true;
	}

	if(empty($checked_sources)){
		$checked_sources   =  array_keys($DOC_SOURCES);
	}

	if(request_method() == "POST"){
		if(!User::logged()){
			$err_msg[] = "Meklētājs tikai reģistrētiem lietotājiem";
			return null;
		}
		$do_log = post('spam') ? false : true;
		$search_q = trim(post('search_q'));
	} else {
		$do_log = false;
		$search_q = trim(get('search_q'));
	}

	$template->set_title("Meklēšana: ".specialchars($search_q));

	$T = $template->add_file("search.tpl");

	$T->set_var("include_comments_checked", $include_comments ? " checked" : "");
	$T->set_var("only_titles_checked", $only_titles ? " checked" : "");

	foreach($DOC_SOURCES as $id=>$sect){
		$T->set_var('source_id', $id);
		$T->set_var('source_name', $sect['name']);
		if(empty($checked_sources) || in_array($id, $checked_sources)){
			$T->set_var('source_checked', ' checked');
		} else {
			$T->set_var('source_checked', '');
		}
		$T->parse_block('BLOCK_search_sources', TMPL_APPEND);
	}

	$T->set_var("doc_count", 0, 'BLOCK_search');
	$T->set_var('search_q', specialchars($search_q));

	// if($search_q && (mb_strlen($search_q) < 3)){
	// 	$err_msg[] = "Jāievada vismaz 3 simbolus";
	// }

	if(!$search_q){
		$T->enable('BLOCK_search_help');
		return $T;
	}

	if($only_titles){
		$index = "doc_titles";
	} else {
		if($include_comments){
			$index = "doc_with_comments";
		} else {
			$index = "doc";
		}
	}

	$params = new SearchParams(
		q:$search_q,
		index:$index,
		filters:['doc_source_id'=>$checked_sources],
		limit:$spx_limit
	);

	# Log
	if($do_log){
		$sql = "INSERT INTO search_log (login_id, sl_q, sl_ip) VALUES (?,?,?)";
		DB::Execute($sql, User::id(), $search_q, User::ip());
	}

	list($res, $spx) = tm_search($params);

	$search_msg = [];
	if($res === false){
		$template->error("Meklētāja kļūda. Ļoti iespējams nekorekta vai nepabeigta meklēšanas izteiksme ar simboliem: ()-!");
		user_error($spx->GetLastError().": $search_q", E_USER_WARNING);
		return $T;
	} elseif($res['total_found'] == 0) {
		$search_msg[] = "Nekas netika atrasts";
	}

	if($res['total_found'] > $spx_limit){
		$search_msg[] = "Uzmanību: atrasti ".$res['total_found']." rezultāti, rādam $spx_limit";
	}

	$T->enable('BLOCK_search');

	if(!empty($res['matches']))
	{
		$T->set_var("doc_count", $res['total_found'], 'BLOCK_search');
		$T->enable('BLOCK_search_item');
		foreach($res['matches'] as $doc){
			$item = $doc['attrs'];
			$item['doc_module_name'] = $DOC_SOURCES[$item['doc_source_id']]['name'];

			if($r = ResEntity::get((int)$item['res_id'])){
				$item['res_route'] = $r->res_route."?hl=".urlencode($search_q);
			} else {
				trigger_error("No res for search item:".printrr($item), E_USER_WARNING);
				$item['res_route'] = "/";
			}

			$item['doc_date'] = date('d.m.Y', $item['doc_entered']);
			$T->set_array($item, 'BLOCK_search_item');
			$T->parse_block('BLOCK_search_item', TMPL_APPEND);
		}
	}

	if($search_msg){
		$T->enable('BLOCK_search_msg')->set_var('search_msg', join("<br/>\n", $search_msg));
	}

	return $T;
}

function search_log(MainModule $template): ?Template
{
	$template->set_title("Ko mēs meklējam");

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

function insert_comment_theme_merge(int $forum_res_id, int $comment_res_id, int $ignored)
{
	$sql = "INSERT INTO res_merge (forum_res_id, comment_res_id, ignored) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE ignored=VALUES(ignored)";

	return DB::Execute($sql, $forum_res_id, $comment_res_id, $ignored);
}

function user_thumb_exists(int $l_id): bool
{
	global $sys_user_root;

	return file_exists(join_paths($sys_user_root, "pic", "thumb", $l_id.".jpg"));
}

function user_image_exists(int $l_id): bool
{
	global $sys_user_root;

	return file_exists(join_paths($sys_user_root, "pic", $l_id.".jpg"));
}

function add_comment(MainTemplate $template, int $res_id, string $res_data, array &$error_msg): bool
{
	if(!User::logged()){
		$template->not_logged();
		return false;
	}

	if(empty($res_data)){
		$error_msg[] = "Kaut kas jau jāieraksta";
	} else {
		if(Res::user_add_comment($res_id, $res_data))
		{
			return true;
		} else {
			$error_msg[] = "Neizdevās pievienot komentāru";
		}
	}

	return false;
}

function update_comment(MainTemplate $template, int $res_id, string $res_data, array &$error_msg): bool
{
	if(!User::logged()){
		$template->not_logged();
		return false;
	}

	if(empty($res_data)){
		$error_msg[] = "Kaut kas jau jāieraksta";
	} else {
		$Res = new ResType(
			res_id: $res_id,
			res_data: $res_data,
			res_data_compiled: parse_text_data($res_data),
		);

		if($Res->update())
		{
			return true;
		} else {
			$error_msg[] = "Neizdevās saglabāt komentāru";
		}
	}

	return false;
}

function login(string $login_or_email, string $passw, string $referer): ?LoginsType
{
	if($login_data = (new Logins)->login($login_or_email, $passw))
	{
		if(!empty($login_data->l_sess_id)){
			session_write_close();
			session_id($login_data->l_sess_id);
			session_start();
		}

		User::data($login_data);

		if(
			empty($referer) ||
			(strpos($referer, "/register/") !== false) ||
			(strpos($referer, "/login/") !== false) ||
			(strpos($referer, "/forgot/") !== false)
			)
		{
			header("Location: /user/profile/");
		} else {
			header("Location: $referer");
		}

		return $login_data;
	} else {
		User::data(null);
		return null;
	}
}
