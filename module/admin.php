<?php declare(strict_types = 1);

$sys_admins = [3];

if(!(user_loged() && in_array($_SESSION['login']['l_id'], $sys_admins))){
	header403();
	return;
}

$admin_modules = array(
	'modules'=>'Moduļi',
	'article'=>'Ziņas',
	'upload'=>'Faili',
	'forum'=>'Forums',
	'editor'=>'',
	'logins'=>'Logini',
	'reports'=>'Reporti',
	'res'=>'',
	'comment'=>'',
	'lang'=>'',
	'index'=>''
);

$admin_module = array_shift($sys_parameters);
$admin_module = (isset($admin_modules[$admin_module]) ? $admin_module : '');
$admin_root = "/admin";
$module_root = "$admin_root/$admin_module";

if(!$admin_module || !file_exists("$sys_root/module/admin/$admin_module.php"))
	$admin_module = 'start';

include("$sys_root/module/admin/$admin_module.php");
