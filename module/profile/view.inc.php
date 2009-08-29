<?php

/*
$atteli_root = $sys_user_root.'/pic';
$atteli_troot = $sys_user_root.'/pic/thumb';
$atteli_http_root = $sys_user_http_root.'/pic';
$atteli_http_troot = $sys_user_http_root.'/pic/thumb';
*/

$login = array_shift($sys_parameters);
$login_data = Logins::load_by_login($login);
//if(!())
//	return;

$pic_path = "$sys_http_root/user/image/$login[l_login]/";
$tpic_path = "$sys_http_root/user/thumb/$login[l_login]/";

$template = new MainModule($sys_template_root, 'atteli', 'tmpl.attels_single.php');
if(!user_loged())
{
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

$pic_path = "$sys_http_root/user/image/$login_data[l_login]/";
//$pic_localpath = $sys_user_root.'/'.$at_id.'.jpg';

if($login_data)
{
	$template->set_var('pic_path', $pic_path);
} else {
	$template->set_var('pic_path', $sys_http_root.'/img/1x1.gif');
}

$template->set_title(" - $login_data[l_nick] bilde");

$template->out();


