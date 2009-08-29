<?php

$l_id = array_shift($sys_parameters);
$template = new MainModule($sys_template_root, 'profile', 'tmpl.profile_single.php');
$template->set_var('error_l_email', '', 'FILE_profile');

if(!user_loged())
{
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

// ja id
$login_data = array();

// ja login
if(Logins::valid_login($l_id))
{
	$login_data = Logins::load_by_login($l_id);
} else {
	if($l_id === strval(intval($l_id)))
	{
		$login_data = Logins::load_by_id($l_id);
	}
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
	$template->set_title("$l_id - neeksistējošs profils");
	$template->enable('BLOCK_no_suck_login');
}

$template->out();

