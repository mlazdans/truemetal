<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new MainModule($sys_template_root, 'profile', 'tmpl.profile_single.php');
$template->set_var('error_l_email', '', 'FILE_profile');

if(!user_loged())
{
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

if($login_data = Logins::load_by_login($login))
{
	$template->set_title(" - $login_data[l_nick]");
	if($login_data['l_emailvisible'] == LOGIN_EMAIL_VISIBLE)
	{
		$template->enable('BLOCK_public_email');
	}
	$template->set_profile($login_data);
} else {
	$template->set_title("$l_id - neeksistējošs profils");
	$template->enable('BLOCK_no_suck_login');
}

$template->out();

