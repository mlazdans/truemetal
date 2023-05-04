<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/CommentDisabled.php');

$action = post('action');
$json = isset($_GET['json']);

if($json)
{
	$template = new MainModule('profile', 'user/profile/user.tpl');
} else {
	$template = new MainModule('profile');
	$template->set_file('FILE_profile', 'user/profile/user.tpl');
	$template->copy_block('BLOCK_middle', 'FILE_profile');
}

if(user_loged())
{
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
			if(isset($_SERVER['HTTP_REFERER']))
				redirect($_SERVER['HTTP_REFERER']);
			else
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
		if($login_data['l_emailvisible'] == Logins::EMAIL_VISIBLE)
		{
			$template->enable('BLOCK_public_email');
		}
		$template->set_profile($login_data);
	} else {
		if(!$json)
		{
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		}

		$template->set_title("$login - neeksistējošs profils");
		$template->enable('BLOCK_no_such_login');
	}
} else {
	$template->enable('BLOCK_not_loged');
	header($_SERVER["SERVER_PROTOCOL"]." 410 Removed from public eyes");
}

if($json)
{
	ob_start();
	$template->out();
	$html = ob_get_clean();

	$jsonData = new StdClass;
	$jsonData->title = "[ TRUEMETAL ".$template->get_title()." ]";
	$jsonData->html = $html;
	header('Content-Type: text/javascript; charset='.$sys_encoding);
	print json_encode($jsonData);
	return;
} else {
	$template->enable('BLOCK_profile_title');
	$template->set_right_defaults();
	$template->out();
}
