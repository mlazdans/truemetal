<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Logins.php');
require_once('lib/MainModule.php');

$action = array_shift($sys_parameters);
if($action == 'logoff')
{
	$my_login = new Logins;
	if($my_login->logoff())
	{
		header("Location: $sys_http_root/");
		exit;
	}
}

$template = new MainModule($sys_template_root, $sys_module_id, 'index.tpl');
$template->set_title($_pointer['_data_']['module_name']);
$template->set_file('FILE_module', 'login.tpl');
$template->copy_block('BLOCK_middle', 'FILE_module');

if(isset($_POST['data']))
{
	$my_login = new Logins;
	$data = post('data');
	if($login_data = $my_login->login($data['login'], $data['password']))
	{
		//$template->set_var('error_msg', 'Pēc lapas lietošanas vēlams nospiest "LOG OFF"');
		if($login_data['l_sessiondata'])
		{
			session_decode($login_data['l_sessiondata']);
		}

		unset($login_data['l_sessiondata']);
		$_SESSION['login'] = $login_data;

		if(empty($data['referer']))
			header("Location: $sys_http_root/user/profile/");
		else
			header("Location: ".urldecode($data['referer']));

		return;
	} else {
		$template->set_var('error_msg', $my_login->error_msg);
		$_SESSION['login'] = array();
	}
}

$template->set_right();
$template->set_login();
$template->set_reviews();
$template->set_poll();
$template->set_search();
$template->set_online();

$template->out();

