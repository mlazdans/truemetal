<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template->enable('BLOCK_forgot_form');

$data = post('data');
if(empty($data['l_email']) && empty($data['l_login']))
{
	$error_msg[] = 'Jānorāda logins vai e-pasts';
	$template->enable('BLOCK_forgot_form');
	return;
}

$test_data = false;

# Meklēt pēc logina
if($data['l_login'])
	$test_data = Logins::load_by_login($data['l_login'], array('l_accepted'=>LOGIN_ALL));

# Meklē pēc e-pasta
if(!$test_data && $data['l_email'])
	$test_data = Logins::load_by_email($data['l_email'], array('l_accepted'=>LOGIN_ALL));

if($test_data)
{
	$l_login = $test_data['l_login'];
	$forgot_code = $logins->insert_forgot_code($l_login);
	if($logins->send_forgot_code($l_login, $forgot_code, $test_data['l_email']))
	{
		$template->set_var('l_email', $test_data['l_email'], 'BLOCK_forgot_ok');
		$template->enable('BLOCK_forgot_ok');
		$template->disable('BLOCK_forgot_form');
	} else {
		$error_msg[] = 'Kautkas nogāja greizi - nevar nosūtīt kodu uz '.$test_data['l_email'];
	}
} else {
	$error_msg[] = 'Šāds lietotājs netika atrasts';
}


