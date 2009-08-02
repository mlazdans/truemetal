<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Logins.php');
require_once('lib/MainModule.php');

// user profile
$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title('Profils');
$template->set_file('FILE_module', 'tmpl.profile.php');
$template->copy_block('BLOCK_middle', 'FILE_module');

$section = array_shift($sys_parameters);

// pop view
if($section == 'view')
{
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
	return;
}
// another user profile
elseif($section == 'user')
{
	//$login = new Logins;
	$l_id = array_shift($sys_parameters);
	$template = new MainModule($sys_template_root, 'profile', 'tmpl.profile_single.php');
	$template->set_file('FILE_profile', 'tmpl.profile.php');
	$template->copy_block('BLOCK_profile_data', 'FILE_profile');
	$template->set_var('error_l_email', '', 'FILE_profile');

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
	return;
} elseif(!user_loged()) {
	$template->enable('BLOCK_not_loged');
} else {

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
	$template->enable('BLOCK_picture_delete');
	$template->enable('BLOCK_private_profile');

} // logged

$template->set_right();
$template->set_login();
$template->set_reviews();
$template->set_poll();
$template->set_search();
$template->set_online();

$template->out();

