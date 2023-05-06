<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new MainModule($sys_module_id);
$template->set_title('E-pasta maiņa');
$template->set_file('FILE_module', 'user/emailch.tpl');
$template->copy_block('BLOCK_middle', 'FILE_module');

# TODO: remove
if(!$i_am_admin){
	$template->enable('BLOCK_maint');
	$template->set_right_defaults();
	$template->out();
	return;
}

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	$template->enable('BLOCK_not_loged');
	$template->set_right_defaults();
	$template->out();
	return;
}

$old_email = trim($_SESSION['login']['l_email']);

$template->enable('BLOCK_emailch_form');
$template->set_var('old_email', specialchars($old_email), 'BLOCK_emailch_form');

if(isset($_POST['data']))
{
	$data = $_POST['data'];
	$new_email = trim($data['new_email'] ?? "");

	$template->set_var('new_email', specialchars($new_email), 'BLOCK_emailch_form');

	$error_msg = [];

	if(empty($new_email)){
		$error_msg[] = "Nav norādīts jaunais e-pasts";
	} else {
		if(strtolower($new_email) == strtolower($old_email)){
			$error_msg[] = "Jaunais e-pasts nav jauns";
		} else {
			if(!valid_email($new_email)) {
				$error_msg[] = 'Nekorekta e-pasta adrese!';
			}
		}
	}

	$result = (function($login, $new_email, &$error_msg){
		global $sys_template_root, $sys_domain;

		$accept_code = Logins::insert_accept_code($login, $new_email);

		if(!$accept_code){
			$error_msg[] = "Datubāzes kļūda: nevar pievienot apstiprināšanas kodu";
			return false;
		}

		$t = new Template($sys_template_root);
		$t->set_file('msg', 'emails/email_changed.tpl');
		$t->set_var('ip', $_SERVER['REMOTE_ADDR']);
		$t->set_var('sys_domain', $sys_domain);
		$t->set_var('code', $accept_code);
		$msg = $t->parse_block('msg');

		$subj = "$sys_domain - e-pasta apstiprināšana";

		if(Logins::send_accept_code($login, $new_email, $subj, $msg))
		{
			return true;
		}

		$error_msg[] = "Nevar nosūtīt kodu uz $new_email";
		if(isset($GLOBALS['php_errormsg']->message)){
			$error_msg[] = '('.$GLOBALS['php_errormsg']->message.')';
		}

		return false;
	})($_SESSION['login']['l_login'], $new_email, $error_msg);

	if($error_msg){
		$template->enable('BLOCK_emailch_error');
		$template->set_var('error_msg', join('<br>', $error_msg), 'BLOCK_emailch_error');
		$template->set_var('error_new_email', ' class="error-form"', 'BLOCK_emailch_form');
	}

	if($result){
		$template->enable('BLOCK_emailch_msg');
		$template->set_var('msg', "Uz jauno e-pastu tika nosūtīts apstiprināšanas kods.", 'BLOCK_emailch_msg');
	}
}

$template->set_right_defaults();
$template->out();
