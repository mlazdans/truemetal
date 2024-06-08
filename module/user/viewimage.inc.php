<?php declare(strict_types = 1);

$l_hash = array_shift($sys_parameters) ?? "";

$template = new MainTemplate();
$T = new ViewimageTemplate;

if(!User::logged())
{
	$template->not_logged();
	$template->set_right_defaults();
	$template->print();
	return;
}

if(!($login_data = Logins::load_by_login_hash($l_hash))){
	$template->not_found();
	$template->set_right_defaults();
	$template->print();
	return;
}

$pic_path = "/user/image/$login_data->l_hash/";

$T->pic_path = $pic_path;
$T->l_nick = $login_data->l_nick;

$template->set_title(" - $login_data->l_nick bilde");
$template->set_right_defaults();
$template->MiddleBlock = $T;
$template->print();
