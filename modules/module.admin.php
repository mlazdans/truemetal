<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//if($_SERVER['REMOTE_ADDR'] != '193.108.185.195')
//	exit;

header('Cache-Control: no-cache');
header('Pragma: no-cache');

function identify() { 
	header("WWW-Authenticate: Basic realm=\"Restricted zone!\"");
	header("HTTP/1.0 401 Unauthorized"); 

	die("Nepareizs login vai parole!"); 
} 

require_once('../classes/class.AdminModule.php');
require_once('../classes/class.User.php');

$user_login = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
$user_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';

$user = new User();
$user->login($user_login, $user_pass);
if($user->logged_id) {
	if(
		!isset($user->data['user_login']) ||
		!isset($user->data['user_pass']) ||
		($user->data['user_login'] != $user_login) ||
		($user->data['user_pass'] != $user_pass)
	)
		identify();
	else
		$_USER = $user->data;
} else
	identify();

$admin_modules = array(
	'modules'=>'Moduļi',
	'article'=>'Ziņas',
	'upload'=>'Faili',
	'poll'=>'Jautājums',
	'forum'=>'Forums',
	'gallery'=>'Galerijas',
	'gallery_group'=>'Galeriju grupas',
	'editor'=>'',
	'user'=>'Lietotāji',
	'ban'=>'Ban',
	'permission'=>'Tiesības',
	'online'=>'Online',
	'logins'=>'Logini',
	'lang'=>'',
	'index'=>''
);

$admin_module = array_shift($sys_parameters);
$admin_module = (isset($admin_modules[$admin_module]) ? $admin_module : '');
$admin_root = "$sys_http_root/admin";
$module_root = "$admin_root/$admin_module";

if(!$admin_module)
	$admin_module = 'start';

if(!file_exists('../modules/admin/module.'.$admin_module.'.php'))
	$admin_module = 'start';

include('../modules/admin/module.'.$admin_module.'.php');

?>
