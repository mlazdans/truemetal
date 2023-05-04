<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$ADMIN = false;
# TODO: rādīt bildes admin daļā, ja useris disablēts
/*
$user_login = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
$user_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

if($user_login && $user_pass)
{

	$user = new User();
	$user->login($user_login, $user_pass);
	if($user->logged_id) {
		if(
			!isset($user->data['user_login']) ||
			!isset($user->data['user_pass']) ||
			($user->data['user_login'] != $user_login) ||
			($user->data['user_pass'] != $user_pass)
		)
			$ADMIN = false;
		else
			$ADMIN = true;
	} else
		$ADMIN = false;
}
*/

$thumb = (isset($thumb) ? $thumb : "");
$login = array_shift($sys_parameters);
$suffix = array_shift($sys_parameters);
$login_data = Logins::load_by_login($login, $ADMIN);

$suffix = preg_replace('/[^-\d]/', '', $suffix);
if(
	user_loged() &&
	$login_data &&
	($pic_localpath = "$sys_user_root/pic$thumb/$login_data[l_id]$suffix.jpg") &&
	($info = getimagesize($pic_localpath)))
{
	$last_modified_time = filemtime($pic_localpath);
	$etag = md5_file($pic_localpath);
	$image_data = file_get_contents($pic_localpath);

	header("Content-type: $info[mime]");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
	header("Etag: $etag");
	header("Expires: ".gmdate("D, d M Y H:i:s", time() + (7 * 24 * 3600)) . " GMT"); // 7d NOTE: keep in sync with nginx

	if(
		(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time)) ||
		(isset($_SERVER['HTTP_IF_NONE_MATCH']) && (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag))
		)
	{
		header("HTTP/1.1 304 Not Modified");
		return;
	}
	print $image_data;
} else {
	header("Content-type: image/gif");
	readfile("$sys_public_root/img/1x1.gif");
}

