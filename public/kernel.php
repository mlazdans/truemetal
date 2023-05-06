<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# TODO: autoload
# TODO: mainot galerijām/foruma/commention utt login_id, trigerī nomainās arī res tabulā

# DEFAULTS - var overraidot configā
$sys_start_time        = microtime(true);
$sys_root              = realpath(dirname(__FILE__).'/../');
$sys_public_root       = $sys_root.'/public';
$sys_template_root     = $sys_root.'/templates';
$sys_upload_root       = $sys_public_root.'/data';
$sys_upload_http_root  = '/data';
$sys_user_root         = $sys_root.'/users';

//$sys_error_reporting   = E_ALL & ~(E_NOTICE | E_STRICT | E_DEPRECATED);
$sys_error_reporting   = E_ALL;
$sys_default_lang      = 'lv';
$sys_encoding          = 'utf-8';
$sys_domain            = (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
$sys_script_version    = 1;
$sys_banned            = array();
$sys_admins            = array('127.0.0.1');
$sys_module_map        = array();
$sys_use_cdn           = false;
$sys_cdn_func          = '';

$sys_nosess_modules    = array('css', 'jsload', 'apc', 'info');

$sys_mail_from         = (isset($_SERVER['SERVER_ADMIN']) ?
	$_SERVER['SERVER_ADMIN'] :
	ini_get('sendmail_from')
	);
if(!$sys_mail_from)
	$sys_mail_from = 'nobody@localhost';

$ip                    = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
$now                   = date("d.m.Y, H:i", time());
$today                 = date("d.m.Y");

# Config
require_once($sys_root.'/include/config.php');

if(!isset($i_am_admin))
	$i_am_admin = in_array($ip, $sys_admins);
if(!isset($sys_debug))
	$sys_debug = ($i_am_admin ? true : false);

/*
if(!$i_am_admin){
	die('Maintenancē, bļa');
}
*/

//ini_set('sysvshm.init_mem', 100000);
ini_set('display_errors', ($sys_debug ? 1 : 0));
ini_set('expose_php', false);
error_reporting($sys_error_reporting);

# Include paths
$include_path = explode(PATH_SEPARATOR, ini_get('include_path'));
foreach($include_path as $k=>$v) # Unset current dir
{
	if($v == '.' || $v == './')
		unset($include_path[$k]);
}
$paths = array(
	".",
	$sys_root,
	);
$include_path = array_merge($paths, $include_path);
ini_set('include_path', join(PATH_SEPARATOR, $include_path));
if(!empty($KERNEL_LEAVE_AFTER_INIT))
	return;

# Bans
if(isset($sys_banned[$ip]))
{
	print "Banned: ".$sys_banned[$ip];
	return;
}

require_once('include/dbconnect.php');
require_once('lib/utils.php');
require_once('lib/MainModule.php');
require_once('lib/Module.php');
require_once('lib/Logins.php');

# MySQL tune
//$db->Execute(sprintf("SET SESSION join_buffer_size=%d", 512*1024));

mb_regex_encoding($sys_encoding);
mb_internal_encoding($sys_encoding);

# dabuujam parametrus no mod_rewrite
if(!isset($_SERVER["SERVER_PROTOCOL"]))
	$_SERVER["SERVER_PROTOCOL"] = "HTTP/1.0";
if(!isset($_SERVER["REQUEST_URI"]))
	$_SERVER["REQUEST_URI"] = "";

$parts = explode('?', $_SERVER["REQUEST_URI"]);
$_SERVER["REQUEST_URI"] = array_shift($parts);
$_SERVER["QUERY_STRING"] = join("?", $parts);

$sys_parameters = explode('/', $_SERVER["REQUEST_URI"]);
$sys_parameters = parse_params($sys_parameters);

$sys_module_id = array_shift($sys_parameters);
# ja nav ne1 modulis selekteets
if(!$sys_module_id && $sys_default_module)
	$sys_module_id = $sys_default_module;

if(!in_array($sys_module_id, $sys_nosess_modules)){
	require_once('include/session_handler.php');
}

register_shutdown_function("tm_shutdown");
if(user_loged())
{
	if($l = Logins::load_by_id($_SESSION['login']['l_id'])) {
		session_decode($l['l_sessiondata']);

		if(is_array($_SESSION['login']??[]))
		{
			foreach($_SESSION['login'] as $k=>&$v)
			{
				if(isset($l[$k]))
				{
					if($v != $l[$k])
					{
						$v = $l[$k];
					}
				}
			}
		}

		$db->Execute("UPDATE logins SET l_lastaccess = CURRENT_TIMESTAMP, l_logedin = 'Y' WHERE l_id = $l[l_id]");
	} else {
		Logins::logoff();
		redirect();
		return;
	}
}

$module_root = "/$sys_module_id";

# nochekojam, vai modulis existee, ja nee tad vai mappings iraid
if(isset($sys_module_map[$sys_module_id]) && !file_exists("$sys_root/module/$sys_module_id.php"))
	$sys_module_id = $sys_module_map[$sys_module_id];

$module = new Module;
$sys_modules = $module_tree = $module->load_tree(0);
$sys_module = !invalid($sys_module_id) &&
	(
		isset($sys_modules[$sys_module_id]) ||
		file_exists("$sys_root/module/$sys_module_id.php") ||
		isset($sys_module_map[$sys_module_id])
	) ?
	$sys_module_id:
	$sys_default_module;

$path = array();
$_pointer = &$module_tree[$sys_module_id];

if(isset($module_tree[$sys_module_id]) && $module_tree[$sys_module_id]['_data_'])
	$path[$sys_module_id] = $module_tree[$sys_module_id]['_data_'];

foreach($sys_parameters as $k=>$v)
{
	if(!isset($_pointer[$v]))
		break;

	$path[$v] = $_pointer['_data_'];
	unset($sys_parameters[$k]);
}

$_GET = _GET();

header('Content-Type: text/html; charset='.$sys_encoding);
header('X-Powered-By: TRUEMETAL');

# LOCALE
if(isset($sys_locale)){
	setlocale(LC_TIME, $sys_locale);
}

# TODO: novākt pēc kāda laika
$clear_session = array(
	'comments',
	'gallery',
	);
foreach($clear_session as $section){
	if(isset($_SESSION[$section])){
		unset($_SESSION[$section]);
	}
}

# iesleedzam vaidziigo moduli
//ob_start();
if(file_exists("$sys_root/module/$sys_module.php")) {
	include("$sys_root/module/$sys_module.php");
} else {
	include("$sys_root/module/$sys_default_module.php");
}

// if($i_am_admin && !in_array($sys_module_id, $sys_nosess_modules)){
// 	$sys_end_time = microtime(true);
// 	$rendered = 'Rendered in: '.number_format(($sys_end_time - $sys_start_time), 4, '.', '').' sec';
// 	echo $rendered;
// }
