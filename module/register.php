<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# Blacklisted
if(user_blacklisted() || Logins::banned24h($GLOBALS['ip']))
{
	print "Blacklisted: $ip";
	return;
}

$action = array_shift($sys_parameters);

$template = new MainModule($sys_module_id, 'index.tpl');
$template->set_title("Reģistrācija");
$template->set_file('FILE_register', 'register.tpl');
$template->copy_block('BLOCK_middle', 'FILE_register');

$logins = new Logins;

$error_msg = array();

if(isset($_POST['data']))
{
	$check = array(
		'l_login',
		'l_nick',
		'l_password',
		'l_email',
	);

	$data = post('data', array());

	$error_field = array();
	foreach($check as $c) {
		$data[$c] = isset($data[$c]) ? trim($data[$c]) : '';
		if(empty($data[$c])){
			$error_field[] = $c;
		}
	}

	$data['l_login'] = strtolower($data['l_login']);

	if(empty($data['l_password'])){
		$error_msg[] = 'Nav ievadīta parole';
		$error_field[] = 'l_password';
		$error_field[] = 'l_password2';
	} else {
		if(!pw_validate($data['l_password'], $data['l_password2'], $error_msg)){
			$error_field[] = 'l_password';
			$error_field[] = 'l_password2';
		}
	}

	if(invalid($data['l_login']) || strlen($data['l_login']) < 5) {
		$error_msg[] = 'Nepareizs vai īss logins';
		$error_field[] = 'l_login';
	}

	if(!valid_email($data['l_email'])) {
		$error_msg[] = 'Nekorekta e-pasta adrese';
		$error_field[] = 'l_email';
	}

	# test login
	if($test_login = $logins->load(['l_login'=>$data['l_login'], 'l_active'=>Res::STATE_ALL, 'l_accepted'=>Res::STATE_ALL])) {
		$error_field[] = 'l_login';
		$error_msg[] = 'Šāds login jau eksistē';
	}

	# test email
	if($test_email = $logins->load(['l_email'=>$data['l_email'], 'l_active'=>Res::STATE_ALL, 'l_accepted'=>Res::STATE_ALL])) {
		$error_field[] = 'l_email';
		$error_msg[] = 'Šāda e-pasta adrese jau eksistē';
	}

	# test nick
	if($test_nick = $logins->load(['l_nick'=>$data['l_nick'], 'l_active'=>Res::STATE_ALL, 'l_accepted'=>Res::STATE_ALL])) {
		$error_field[] = 'l_nick';
		$error_msg[] = 'Šāds segvārds jau eksistē';
	}

	if($error_field)
	{
		foreach($error_field as $k)
			$template->set_var('error_'.$k, ' class="error-form"', 'BLOCK_register_form');

		parse_form_data_array($data);
		$template->set_array($data, 'BLOCK_register_form');
	} elseif(($id = $logins->insert($data))) {
		email($new_login_mail, '[truemetal] jauns lietotajs', "$data[l_login] ($data[l_nick])\n\nIP:$_SERVER[REMOTE_ADDR]");
		header("Location: $module_root/ok/");
		return;
	}
}

if($action == 'ok')
{
	$template->enable('BLOCK_register_ok');
} elseif($action == 'accept') {
	$code = array_shift($sys_parameters);
	if($logins->accept_login($code))
	{
		$template->enable('BLOCK_accept_ok');
	} else {
		$template->enable('BLOCK_accept_error');
	}
} else {
	$template->enable('BLOCK_register_form');
}

if($error_msg)
{
	$template->enable('BLOCK_register_error');
	$template->set_var('error_msg', join('<br/>', $error_msg), 'BLOCK_register_error');
}

$template->set_right_defaults();
$template->out();

