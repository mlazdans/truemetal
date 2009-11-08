<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Logins.php');
require_once('lib/MainModule.php');

$action = array_shift($sys_parameters);

$template = new MainModule($sys_template_root, $sys_module_id, 'index.tpl');
$template->set_title("Reģistrācija");
$template->set_file('FILE_register', 'register.tpl');
$template->copy_block('BLOCK_middle', 'FILE_register');

$logins = new Logins;

$error_msg = array();

if(isset($_POST['data']))
{
	$check = array(
		'l_email',
		'l_login',
		'l_nick',
		'l_password'
	);

	$data = post('data', array());

	$error_field = array();
	//$template->set_var('error_'.$c, ' class="error-form"');
	foreach($check as $c)
	{
		if(empty($data[$c]))
			$error_field[] = 'error_'.$c;
	}

	if(empty($data['l_login']))
		$data['l_login'] = '';

	if($data['l_password'] != $data['l_password2'])
	{
		//$error = true;
		$error_msg[] = 'Paroles nesakrīt!';
		$error_field[] = 'l_password';
		$error_field[] = 'l_password2';
	} elseif(invalid($data['l_password']) || strlen($data['l_password']) < 5) {
		$error_msg[] = 'Nepareiza vai īsa parole!';
		$error_field[] = 'l_password';
	} elseif(invalid($data['l_login']) || strlen($data['l_login']) < 5) {
		$error_msg[] = 'Nepareizs vai īss logins!';
		$error_field[] = 'l_login';
	} elseif(!valid_email($data['l_email'])) {
		$error_msg[] = 'Nekorekta e-pasta adrese!';
		$error_field[] = 'l_email';
	}

	$data['l_login'] = strtolower($data['l_login']);
	$data['l_email'] = trim($data['l_email']);

	if($test_login = $logins->load(array(
		'l_login'=>$data['l_login'],
		'l_active'=>LOGIN_ALL,
		'l_accepted'=>LOGIN_ALL,
		)))
	{
		//$error = true;
		//$template->set_var('error_l_login', ' class="error-form"');
		$error_field[] = 'l_login';
		$error_msg[] = 'Šāds login jau eksistē!';
	} // test login

	if($test_email = $logins->load(array(
		'l_email'=>$data['l_email'],
		'l_active'=>LOGIN_ALL,
		'l_accepted'=>LOGIN_ALL,
		)))
	{
		//$error = true;
		//$template->set_var('error_l_email', ' class="error-form"');
		$error_field[] = 'l_email';
		$error_msg[] = 'Šāda e-pasta adrese jau eksistē!';
	} // test email

	if($test_nick = $logins->load(array(
		'l_nick'=>$data['l_nick'],
		'l_active'=>LOGIN_ALL,
		'l_accepted'=>LOGIN_ALL,
		)))
	{
		//$error = true;
		//$template->set_var('error_l_nick', ' class="error-form"');
		$error_field[] = 'l_nick';
		$error_msg[] = 'Šāds niks jau eksistē!';
	} // test email

	if($error_field)
	{
		foreach($error_field as $k)
			$template->set_var('error_'.$k, ' class="error-form"', 'BLOCK_register_form');

		parse_form_data_array($data);
		$template->set_array($data, 'BLOCK_register_form');
	} elseif(($id = $logins->insert($data))) {
		email($new_login_mail, '[truemetal] jauns lietotajs', "$data[l_login] ($data[l_nick])\n\nIP:$_SERVER[REMOTE_ADDR]");
		//email($data['l_email'], 'truemetal.lv registacija', "Veiksmigi registrejaties\nGaidiet apstiprinajumu!\n\nwww.lpa.lv");
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

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_search();

$template->out();

