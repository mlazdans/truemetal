<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// Admin moduļu kernelis

header('Cache-Control: no-cache');
header('Pragma: no-cache');

function identify() {
	header("WWW-Authenticate: Basic realm=\"Restricted zone!\"");
	header("HTTP/1.0 401 Unauthorized");

	die("Nepareizs logins vai parole!");
}

require_once('lib/AdminModule.php');
require_once('lib/User.php');

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
	'online'=>'Online',
	'logins'=>'Logini',
	'lang'=>'',
	'index'=>''
);

$admin_module = array_shift($sys_parameters);
$admin_module = (isset($admin_modules[$admin_module]) ? $admin_module : '');
$admin_root = "$sys_http_root/admin";
$module_root = "$admin_root/$admin_module";

if(!$admin_module || !file_exists("$sys_root/module/admin/$admin_module.php"))
	$admin_module = 'start';

include("$sys_root/module/admin/$admin_module.php");

