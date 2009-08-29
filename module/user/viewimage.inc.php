<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$login = array_shift($sys_parameters);
$login_data = Logins::load_by_login($login);

$template = new MainModule($sys_template_root, 'atteli', 'user/tmpl.viewimage.php');

if(!user_loged())
{
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

if(
	$login_data &&
	($pic_path = "$sys_http_root/user/image/$login_data[l_login]/")
	)
{
	$template->set_title(" - $login_data[l_nick] bilde");
	$template->set_var('pic_path', $pic_path);
} else {
	$template->set_title(" - not found");
	$template->set_var('pic_path', $sys_http_root.'/img/1x1.gif');
}

$template->out();


