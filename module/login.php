<?php declare(strict_types = 1);

/*
# Blacklisted
if(user_blacklisted())
{
	print "Blacklisted: $ip";
	return;
}
*/

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

$template = new MainModule($sys_module_id);
$template->set_title($_pointer['_data_']['module_name']??'');

$T = $template->add_file('login.tpl');

if(isset($_POST['data']))
{
	$my_login = new Logins;
	$data = post('data');
	if($login_data = $my_login->login($data['login']??"", $data['password']??""))
	{
		if($login_data['l_sessiondata']){
			session_decode($login_data['l_sessiondata']);
		}

		unset($login_data['l_sessiondata']);
		unset($login_data['l_password']);
		$_SESSION['login'] = $login_data;

		$referer = empty($data['referer']) ? false : urldecode($data['referer']);
		if(
			empty($data['referer']) ||
			(strpos($referer, "/register/") !== false) ||
			(strpos($referer, "/login/") !== false) ||
			(strpos($referer, "/forgot/") !== false)
			)
		{
			header("Location: /user/profile/");
		} else {
			header("Location: $referer");
		}

		return;
	} else {
		$T->enable('BLOCK_login_err');
		$T->set_var('error_msg', 'Nepareizs login vai parole!', 'BLOCK_login_err');
		$T->set_array($data, 'FILE_module');
		$_SESSION['login'] = array();
	}
}

$template->set_right_defaults();
$template->out($T);
