<?php declare(strict_types = 1);

$action = array_shift($sys_parameters);
if($action == 'logoff')
{
	if(Logins::logoff())
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
		User::data($login_data);

		$referer = empty($data['referer']) ? false : $data['referer'];
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
		$T->set_var('error_msg', 'Nepareizs login vai parole!');
		$T->set_array($data);
		User::data(null);
	}
}

$template->set_right_defaults();
$template->out($T);
