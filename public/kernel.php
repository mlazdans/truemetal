<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// galvenais fails - kernelis :)

# DEFAULTS - var overraidot configā
$sys_start_time        = microtime(true);
$sys_root              = realpath(dirname(__FILE__).'/../');
$sys_public_root       = $sys_root.'/public';
$sys_http_root         = '';
$sys_template_root     = $sys_root.'/templates';
$sys_upload_root       = $sys_public_root.'/data';
$sys_upload_http_root  = '/data';
$sys_user_root         = $sys_root.'/users';

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
require_once("$sys_root/include/config.php");

if(!isset($i_am_admin))
	$i_am_admin = in_array($ip, $sys_admins);
if(!isset($sys_debug))
	$sys_debug = ($i_am_admin ? true : false);

ini_set('display_errors', ($sys_debug ? 1 : 0));
ini_set('expose_php', false);
error_reporting($sys_error_reporting);

# Include paths
$include_path = split(PATH_SEPARATOR, ini_get('include_path'));
foreach($include_path as $k=>$v) // Unset current dir
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

//apd_set_pprof_trace();
/* some includes */
require_once('include/dbconnect.php');
require_once('include/session_handler.php');
require_once('lib/utils.php');
require_once('lib/MainModule.php');
require_once('lib/Module.php');
require_once('lib/Logins.php');

mb_regex_encoding($sys_encoding);
mb_internal_encoding($sys_encoding);

if(user_loged())
{
	if($l = Logins::load_by_id_logged_in($_SESSION['login']['l_id']))
	{
		//session_decode($l['l_sessiondata']);
	} else {
		Logins::logoff();
		redirect();
		return;
	}
}

register_shutdown_function("shutdown");

/* dabuujam parametrus no mod_rewrite */
$parts = explode('?', $_SERVER["REQUEST_URI"]);
$_SERVER["REQUEST_URI"] = array_shift($parts);
$_SERVER["QUERY_STRING"] = join("?", $parts);

# NOTE: $sys_http_root netiek ņemts vērā, bet vajadzētu
$sys_parameters = split('/', $_SERVER["REQUEST_URI"]);
$sys_parameters = parse_params($sys_parameters);

$sys_module_id = array_shift($sys_parameters);
// ja nav ne1 modulis selekteets
if(!$sys_module_id && $sys_default_module)
	$sys_module_id = $sys_default_module;

$module_root = "$sys_http_root/$sys_module_id";

// nochekojam, vai modulis existee, ja nee tad vai mappings iraid
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

if($module_tree[$sys_module_id]['_data_'])
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
if(isset($sys_locale))
{
	setlocale(LC_TIME, $sys_locale);
}

//ob_start();
/* iesleedzam vaidziigo moduli */
if(file_exists("$sys_root/module/$sys_module.php")) {
	include("$sys_root/module/$sys_module.php");
} else {
	include("$sys_root/module/$sys_default_module.php");
}
//$data = ob_get_clean();

/*
$tidy_config = array(
	'wrap'=>false,
	'output-xhtml'=>true,
	'doctype'=>'strict',
	);
$tidy = tidy_parse_string($data, $tidy_config, 'UTF8');
$tidy->cleanRepair();
print $tidy;
*/

//$my_login = new Logins;
//$my_login->save_session_data();
//Logins::save_session_data();

function shutdown()
{
	session_commit();
	Logins::save_session_data();
} // shutdown

/*
if($i_am_admin)
{
	$sys_end_time = microtime(true);
	print 'Finished: '.number_format(($sys_end_time - $sys_start_time), 2, '.', '');
}
*/


