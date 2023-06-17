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

# TODO: rate limit
if(isset($_POST['data']))
{
	$data = post('data');
	if(login($data['login']??"", $data['password']??""))
	{
		return;
	} else {
		$T = $template->add_file('login.tpl');
		$T->enable('BLOCK_login_err');
		$T->set_var('error_msg', 'Nepareizs login vai parole!');
		$T->set_array(specialchars($data));
	}
}

$template->set_right_defaults();
$template->out($T??null);
