<?php

$atteli_root = $sys_user_root.'/pic';
$atteli_troot = $sys_user_root.'/pic/thumb';
$atteli_http_root = $sys_user_http_root.'/pic';
$atteli_http_troot = $sys_user_http_root.'/pic/thumb';

$at_id = (int)array_shift($sys_parameters);
//$login = new Logins;
//$login_data = $login->load_by_id($at_id);
if(!($login_data = Logins::load_by_id($at_id)))
	return;

$template = new MainModule($sys_template_root, 'atteli', 'tmpl.attels_single.php');
if(!user_loged())
{
	$template->enable('BLOCK_not_loged');
	$template->out();
	return;
}

$pic_path = $atteli_http_root.'/'.$at_id.'.jpg';
$pic_localpath = $atteli_root.'/'.$at_id.'.jpg';

if(file_exists($pic_localpath))
{
	$template->set_var('pic_path', $pic_path);
} else {
	$template->set_var('pic_path', $sys_http_root.'/img/1x1.gif');
}

$template->set_title(" - $login_data[l_nick] bilde");

$template->out();


