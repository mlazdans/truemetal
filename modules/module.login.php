<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.Logins.php');
require_once('../classes/class.MainModule.php');

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

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_title($_pointer['_data_']['module_name']);
$template->set_file('FILE_module', 'tmpl.login.php');
$template->copy_block('BLOCK_middle', 'FILE_module');

$path = array('map'=>array('module_id'=>'login', 'module_name'=>'LOGIN'));

if(isset($_POST['data']))
{
	$my_login = new Logins;
	$data = &$_POST['data'];
	if($data = $my_login->login($data['login'], $data['password']))
	{
		//$template->set_var('error_msg', 'Pēc lapas lietošanas vēlams nospiest "LOG OFF"');
		if($data['l_sessiondata'])
		{
			session_decode($data['l_sessiondata']);
		}

		unset($data['l_sessiondata']);
		$_SESSION['login'] = $data;
		header("Location: $sys_http_root/profile/");
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
$template->set_calendar();

$template->out();

?>
