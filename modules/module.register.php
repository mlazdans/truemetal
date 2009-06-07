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
$template->set_title("Reģistrācija");
$template->set_file('FILE_register', 'tmpl.register.php');
$template->copy_block('BLOCK_middle', 'FILE_register');

$path = array('map'=>array('module_id'=>'register', 'module_name'=>'REGISTER'));

$template->set_right();
$template->set_login();
$template->set_reviews();
$template->set_poll();
$template->set_search();
$template->set_online();
$template->set_calendar();

$logins = new Logins;

if(isset($_POST['data']))
{
	$check = array
	(
		'l_email',
		'l_login',
		'l_nick',
		'l_password'
	);

	$data = &$_POST['data'];

	$error = false;
	foreach($check as $c)
		if(!isset($data[$c]) || !$data[$c])
		{
			$error = true;
			$template->set_var('error_'.$c, ' class="error-form"');
		}

	foreach($data as $k=>$v)
	{
		my_strip_tags($v);
		$template->set_var($k, $v);
	}

	// nocheko, vai logins jau nav taads
	if(!$error)
	{
		if($data['l_password'] != $data['l_password2'])
		{
			$error = true;
			$template->set_var('error_l_password', ' class="error-form"');
			$template->set_var('error_l_password2', ' class="error-form"');
			$template->set_var('error_msg', 'Paroles nesakrīt!');
			$template->enable('BLOCK_register_error');
		} elseif(invalid($data['l_password']) || strlen($data['l_password']) < 5) {
			$error = true;
			$template->set_var('error_l_password', ' class="error-form"');
			$template->set_var('error_msg', 'Nepareiza vai īsa parole!');
			$template->enable('BLOCK_register_error');
		} elseif(invalid($data['l_login']) || strlen($data['l_login']) < 5) {
			$error = true;
			$template->set_var('error_l_login', ' class="error-form"');
			$template->set_var('error_msg', 'Nepareizs vai īsa logins!');
			$template->enable('BLOCK_register_error');
		} elseif(!valid_email($data['l_email'])) {
			$error = true;
			$template->set_var('error_l_email', ' class="error-form"');
			$template->set_var('error_msg', 'Nekorekta e-pasta adrese!');
			$template->enable('BLOCK_register_error');
		}
	}

	if(!$error)
	{
		$data['l_login'] = strtolower($data['l_login']);
		$data['l_email'] = trim($data['l_email']);

		$error_msg = '';
		if($test_login = $logins->load_by_login_ex($data['l_login']))
		{
			$error = true;
			$template->set_var('error_l_login', ' class="error-form"');
			$error_msg .= 'Šāds login jau eksistē!<br>';
		} // test login

		if($test_email = $logins->load_by_email($data['l_email']))
		{
			$error = true;
			$template->set_var('error_l_email', ' class="error-form"');
			$error_msg .= 'Šāda e-pasta adrese jau eksistē!<br>';
		} // test email

		if($test_nick = $logins->load_by_nick($data['l_nick']))
		{
			$error = true;
			$template->set_var('error_l_nick', ' class="error-form"');
			$error_msg .= 'Šāds niks jau eksistē!<br>';
		} // test email

		if($error_msg)
		{
			$template->set_var('error_msg', $error_msg);
			$template->enable('BLOCK_register_error');
		}
	}

	if(!$error && ($id = $logins->insert($data)))
	{
		email($new_login_mail, '[truemetal] jauns lietotajs', "$data[l_login] ($data[l_nick])\n\nIP:$_SERVER[REMOTE_ADDR]");
		//email($data['l_email'], 'truemetal.lv registacija', "Veiksmigi registrejaties\nGaidiet apstiprinajumu!\n\nwww.lpa.lv");
		header("Location: $module_root/ok/");
	}
}

if($action == 'ok')
	$template->enable('BLOCK_register_ok');
elseif($action == 'accept')
{
	$code = array_shift($sys_parameters);
	if($logins->accept_login($code))
		$template->enable('BLOCK_register_code_ok');
	else {
		$template->set_var('error_msg', "Diemžēl loginu neizdevās apstiprināt!<br><br>Varianti:<br>1) nokavēts 3 x 24h apstiprināšanas termiņš<br>2) nepareizs kods<br>3) logins jau ir apstiprināts<br><br>Ja kas, raksti uz info [at] truemetal.lv");
		$template->enable('BLOCK_register_error');
	}
} else {
	$template->enable('BLOCK_register_form');
	$template->set_var("l_login", '', 'BLOCK_register_form');
	$template->set_var("l_nick", '', 'BLOCK_register_form');
	$template->set_var("l_email", '', 'BLOCK_register_form');
}

$template->out();

