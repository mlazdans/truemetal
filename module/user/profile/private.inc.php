<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$module_root = "$module_root/profile";

if(!user_loged())
{
	header("HTTP/1.1 403 Forbidden");
	$template->enable('BLOCK_not_loged');
	return;
}

# del image
if($section == 'deleteimage')
{
	//$login = new Logins;
	if(Logins::delete_image())
	{
		header("Location: $module_root/");
		return;
	} else {
		$template->enable('BLOCK_profile_error');
		$template->set_var('error_msg', 'Bildi neizdevās izdzēst!');
	}
}

// save
if(isset($_POST['data']))
{
	$login = new Logins;
	$login_data = $_POST['data'];

	if($data = $login->update_profile($login_data))
	{
		unset($data['l_sessiondata']);
		$_SESSION['login'] = $data;
		header("Location: $module_root/");
		return;
	} else {
		$template->enable('BLOCK_profile_error');
		$template->set_var('error_msg', $login->error_msg);
	}
	$login_data = array_merge($_SESSION['login'], $login_data);
} else {
	$login_data = $_SESSION['login'];
} // post

$set_vars = array(
	'user_pic_w'=>$user_pic_w,
	'user_pic_h'=>$user_pic_h,
	'user_pic_tw'=>$user_pic_tw,
	'user_pic_th'=>$user_pic_th
);
$template->set_array($set_vars);
$template->set_profile($login_data);
//$template->enable('BLOCK_picture_delete');
//$template->enable('BLOCK_private_profile');


