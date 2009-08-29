<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$login = array_shift($sys_parameters);
$login_data = Logins::load_by_login($login);

if(
	user_loged() &&
	$login_data &&
	($pic_localpath = "$sys_user_root/pic/thumb/$login_data[l_id].jpg") &&
	($info = getimagesize($pic_localpath)))
{
	header("Content-type: $info[mime]");
	readfile($pic_localpath);
} else {
	header("Content-type: image/gif");
	readfile("$sys_public_root/img/1x1.gif");
}

