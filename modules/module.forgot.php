<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.Logins.php');
require_once('../classes/class.MainModule.php');

$action = array_shift($sys_parameters);

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_title("Aizmirsu paroli");
$template->set_file('FILE_forgot', 'tmpl.forgot.php');
$template->copy_block('BLOCK_middle', 'FILE_forgot');

$template->set_right();
$template->set_login();
$template->set_reviews();
$template->set_poll();
$template->set_search();
$template->set_online();
$template->set_calendar();

$logins = new Logins;

$empty_data = array(
	'l_login'=>'',
	'l_email'=>'',
);

$data = $empty_data;

if($action == 'request')
{
	$data = $_POST['data'];
	if(!$data['l_email'] && !$data['l_login'])
	{
		$template->set_var('error_msg', 'Jānorāda logins vai e-pasts', 'BLOCK_forgot_error');
		$template->enable('BLOCK_forgot_error');
		$template->enable('BLOCK_forgot_form');
	} else {
		$found = false;
		if($data['l_login'])
		{
			if($test_data = $logins->load_by_login($data['l_login']))
			{
				$found = true;
			}
		}

		if(!$found && $data['l_email'])
		{
			if($test_data = $logins->load_by_email($data['l_email']))
			{
				$found = true;
			}
		}

		if($found)
		{
			$l_login = $test_data['l_login'];
			$forgot_code = $logins->insert_forgot_code($l_login);
			if($logins->send_forgot_code($l_login, $forgot_code, $test_data['l_email']))
			{
				$template->set_var('l_email', $test_data['l_email'], 'BLOCK_forgot_ok');
				$template->enable('BLOCK_forgot_ok');
			} else {
				$template->set_var('error_msg', 'Kautkas nogāja greizi - nevar nosūtīt kodu uz '.$test_data['l_email'], 'BLOCK_forgot_error');
				$template->enable('BLOCK_forgot_error');
			}
			//send_forgot_code
		} else {
			$template->set_var('error_msg', 'Šāds lietotājs netika atrasts', 'BLOCK_forgot_error');
			$template->enable('BLOCK_forgot_error');
			$template->enable('BLOCK_forgot_form');
		}
	}
} elseif($action == 'accept') {
	$code = array_shift($sys_parameters);
	$forgot_data = $logins->get_forgot($code);

	if($forgot_data)
	{
		$template->set_var('f_code', $code, 'BLOCK_forgot_passw');

		$data = isset($_POST['data']) ? $_POST['data'] : array();
		$change_passw = isset($_POST['change_passw']) ? $_POST['change_passw'] : '';
		$login_data = $logins->load_by_login($forgot_data['f_login']);

		$pass_changed = false;
		if($change_passw)
		{
			$error_msg = '';
			if(!$data['l_password'])
			{
				$template->set_var('error_msg', 'Parole jānorāda obligāti!', 'BLOCK_forgot_error');
				$template->enable('BLOCK_forgot_error');
			} else {
				// check pass match
				if($data['l_password'] != $data['l_password2'])
				{
					$error_msg .= 'Paroles nesakrīt!<br>';
				} elseif(invalid($data['l_password']) || strlen($data['l_password']) < 5) {
					$error_msg .= 'Nepareiza vai īsa parole!';
				}

				if($error_msg)
				{
					$template->set_var('error_msg', $error_msg, 'BLOCK_forgot_error');
					$template->enable('BLOCK_forgot_error');
				} else {
					if(
						$logins->update_password($login_data['l_login'], $data['l_password']) &&
						$logins->remove_forgot_code($code)
					)
					{
						$pass_changed = true;
					}
				}
			}
		}

		if($pass_changed)
		{
			$template->enable('BLOCK_forgot_code_ok');
		} else {
			$template->set_array($login_data, 'BLOCK_forgot_passw');
			$template->enable('BLOCK_forgot_passw');
		}
	} else {
		$template->set_var('error_msg', "Diemžēl šāds pieprasījums netika atrasts!<br><br>Varianti:<br>1) nokavēts 3 x 24h apstiprināšanas termiņš<br>2) nepareizs kods<br><br>Ja kas, raksti uz info [at] truemetal.lv");
		$template->enable('BLOCK_forgot_error');
	}
} else {
	$template->enable('BLOCK_forgot_form');
}

parse_form_data_array($data);
$template->set_array($data, 'BLOCK_forgot_form');

$template->out();

