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
$login_data = Logins::load_by_login($forgot_data['f_login'], array('l_accepted'=>Res::STATE_ALL));

$pass_changed = false;
if($change_passw) {
	$error_msg = [];
	pw_validate($data['l_password']??"", $data['l_password2']??"", $error_msg);

	if(
		!$error_msg &&
		$logins->accept($login_data['l_id']) &&
		$logins->remove_forgot_code($code) &&
		$logins->update_password($login_data['l_login'], $data['l_password'])
	) {
		header("Location: $module_root/accept/ok/");
		return;
	}
	$template->set_array($data, 'BLOCK_forgot_passw');
}

$template->set_array_except(['l_password', 'l_sessiondata'], $login_data, 'BLOCK_forgot_passw');
$template->enable('BLOCK_forgot_passw');
