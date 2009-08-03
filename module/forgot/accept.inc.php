<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$code = array_shift($sys_parameters);
if($code == 'ok')
{
	$template->enable('BLOCK_forgot_code_ok');
	return;
}

$forgot_data = $logins->get_forgot($code);

if(!$forgot_data)
{
	$template->enable('BLOCK_forgot_code_error');
	return;
}

$template->set_var('f_code', $code, 'BLOCK_forgot_passw');

$data = post('data', array());
$change_passw = (int)post('change_passw');
$login_data = Logins::load_by_login($forgot_data['f_login']);

$pass_changed = false;
if($change_passw)
{
	if(empty($data['l_password']) || empty($data['l_password2']))
		$error_msg[] = 'Parole jānorāda obligāti!';
	if($data['l_password'] != $data['l_password2'])
		$error_msg[] ='Paroles nesakrīt!';
	if(invalid($data['l_password']) || strlen($data['l_password']) < 5)
		$error_msg[] = 'Nepareiza vai īsa parole!';

	if(
		!$error_msg &&
		$logins->update_password($login_data['l_login'], $data['l_password']) &&
		$logins->remove_forgot_code($code)
		)
	{
		header("Location: $module_root/accept/ok/");
		return;
	}
}

$template->set_array($login_data, 'BLOCK_forgot_passw');
$template->enable('BLOCK_forgot_passw');


