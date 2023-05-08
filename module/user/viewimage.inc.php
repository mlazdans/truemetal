<?php declare(strict_types = 1);

$l_hash = array_shift($sys_parameters)??"";

$template = new MainModule('atteli');
$T = $template->add_file('user/viewimage.tpl');

if(!user_loged())
{
	$template->not_logged();
	$template->set_right_defaults();
	$template->out(null);
	return;
}

if(!($login_data = Logins::load_by_login_hash($l_hash))){
	$template->not_found();
	$template->set_right_defaults();
	$template->out(null);
	return;
}

$pic_path = "/user/image/$login_data[l_hash]/";

$T->enable("BLOCK_userpic");

$T->set_var('pic_path', $pic_path);
$T->set_var('l_nick', $login_data['l_nick']);

$template->set_title(" - $login_data[l_nick] bilde");
$template->set_right_defaults();
$template->out($T);

