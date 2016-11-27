<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$login = array_shift($sys_parameters);
$login_data = Logins::load_by_login($login);

//$template = new MainModule($sys_template_root, 'atteli', 'user/viewimage.tpl');
$template = new MainModule($sys_template_root, 'atteli');
$template->set_file('FILE_viewimage', 'user/viewimage.tpl');
$template->copy_block('BLOCK_body', 'FILE_viewimage');

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

$template->enable("BLOCK_userpic");

if(
	$login_data &&
	($pic_path = "/user/image/$login_data[l_login]/")
	)
{
	$template->set_title(" - $login_data[l_nick] bilde");
	$template->set_var('pic_path', $pic_path);
} else {
	$template->set_title(" - not found");
	$template->set_var('pic_path', '/img/1x1.gif');
}

$template->out();


