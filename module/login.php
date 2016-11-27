<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# Blacklisted
if(user_blacklisted())
{
	print "Blacklisted: $ip";
	return;
}

$action = array_shift($sys_parameters);
if($action == 'logoff')
{
	$my_login = new Logins;
	if($my_login->logoff())
	{
		header("Location: /");
		return;
	}
}

$template = new MainModule($sys_template_root, $sys_module_id, 'index.tpl');
$template->set_title($_pointer['_data_']['module_name']);
$template->set_file('FILE_module', 'login.tpl');
$template->copy_block('BLOCK_middle', 'FILE_module');

if(isset($_POST['data']))
{
	$my_login = new Logins;
	$data = post('data');
	if($login_data = $my_login->login($data['login'], $data['password']))
	{
		if($login_data['l_sessiondata']){
			session_decode($login_data['l_sessiondata']);
		}

		unset($login_data['l_sessiondata']);
		$_SESSION['login'] = $login_data;

		$referer = empty($data['referer']) ? false : urldecode($data['referer']);
		if(
			empty($data['referer']) ||
			(strpos($referer, "/register/") !== false) ||
			(strpos($referer, "/forgot/") !== false)
			)
		{
			header("Location: /user/profile/");
		} else {
			header("Location: $referer");
		}

		return;
	} else {
		$template->set_var('error_msg', $my_login->error_msg);
		$_SESSION['login'] = array();
	}
}

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_search();

$template->out();

