<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/CommentDisabled.php');

$template = new MainModule($sys_template_root, 'profile', 'user/profile/user.tpl');
$template->set_var('error_l_email', '', 'FILE_profile');

if(!user_loged())
{
	//header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	header($_SERVER["SERVER_PROTOCOL"]." 410 Removed from public eyes");
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

$action = post('action');
$login_data = Logins::load_by_login($login);

# Disable comments
if(
	$login_data &&
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
		redirect();
		return;
	}
}

if(
	$login_data &&
	($disable_comments_checked = CommentDisabled::get($_SESSION['login']['l_id'], $login_data['l_id']))
	)
{
	$template->set_var('disable_comments_checked', ' checked="checked"', 'BLOCK_profile');
} else {
	$template->set_var('disable_comments_checked', '', 'BLOCK_profile');
}

if(
	$login_data &&
	($_SESSION['login']['l_id'] != $login_data['l_id'])
	)
{
	$template->enable('BLOCK_disable_comments');
}

if($login_data)
{
	$template->set_title(" - $login_data[l_nick]");
	if($login_data['l_emailvisible'] == LOGIN_EMAIL_VISIBLE)
	{
		$template->enable('BLOCK_public_email');
	}
	$template->set_profile($login_data);
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$template->set_title("$login - neeksistējošs profils");
	$template->enable('BLOCK_no_such_login');
}

$template->out();

