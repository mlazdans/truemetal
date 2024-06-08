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

$template = new MainTemplate($sys_module_id);
$template->set_title($_pointer['_data_']['module_name']??'');

$T = new LoginTemplate;

# TODO: rate limit
if(isset($_POST['data']))
{
	$data = post('data');
	if(login($data['login']??"", $data['password']??"", $data['referer']??""))
	{
		return;
	} else {
		$T->error_msg = 'Nepareizs login vai parole!';
		$T->login = $data['login'] ?? '';
		$T->password = $data['password'] ?? '';
		$T->referer = $data['referer'] ?? '';
	}
}

$template->set_right_defaults();
$template->MiddleBlock = $T;
$template->print();
