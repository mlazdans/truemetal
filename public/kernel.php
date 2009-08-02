<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// galvenais fails - kernelis (speeciigi teikts, vai ne? :))

# Defaults - var overraidot configā
$sys_root              = realpath(dirname(__FILE__).'/../');
//$sys_root              = str_replace('\\', '/', $sys_root);
$sys_public_root       = $sys_root.'/public';
$sys_http_root         = '';
$sys_template_root     = $sys_root.'/templates';
$sys_upload_root       = $sys_public_root.'/data';
$sys_upload_http_root  = '/data';
$sys_user_root         = $sys_public_root.'/users';
$sys_user_http_root    = '/users';

$sys_error_reporting   = E_ALL;
$sys_default_lang      = 'lv';
$sys_encoding          = 'utf-8';
$sys_domain            = (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
$sys_script_version    = 1;
$sys_banned            = array();
$sys_admins            = array('127.0.0.1');

$sys_mail_from         = (isset($_SERVER['SERVER_ADMIN']) ?
	$_SERVER['SERVER_ADMIN'] :
	ini_get('sendmail_from')
	);
if(!$sys_mail_from)
	$sys_mail_from = 'nobody@localhost';

$ip                    = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
$now                   = date("d.m.Y, H:i", time());
$today                 = date("d.m.Y");

require_once('../includes/inc.config.php');

if(!isset($i_am_admin))
	$i_am_admin = in_array($ip, $sys_admins);
if(!isset($sys_debug))
	$sys_debug = ($i_am_admin ? true : false);

ini_set('display_errors', ($sys_debug ? 1 : 0));
error_reporting($sys_error_reporting);


$include_path = split(PATH_SEPARATOR, ini_get('include_path'));
# Unset current dir
foreach($include_path as $k=>$v)
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

if(isset($sys_banned[$ip]))
{
	print "Banned: ".$sys_banned[$ip];
	return;
}

/*
if($i_am_admin)
{
print_r($_SERVER);
	$ru = split('/', $_SERVER['REQUEST_URI']);
	array_shift($ru);
	$ru = join('/', $ru);
	if(isset($_SERVER['HTTP_HOST']))
		if($_SERVER['HTTP_HOST'] != 'truemetal.lv')
		{
			header("Location: http://truemetal.lv/".$ru);
			return;
		}
}
*/

//apd_set_pprof_trace();

header("Cache-Control: ");
header("Expires: ");
header("Pragma: ");
header('Content-Type: text/html; charset='.$sys_encoding);

$module_map = array(
);

/* some includes */
require_once('../includes/inc.dbconnect.php');
require_once('../includes/inc.session_handler.php');
require_once('../includes/inc.utils.php');
require_once('lib/MainModule.php');
require_once('lib/Module.php');
require_once('lib/Logins.php');

mb_regex_encoding($sys_encoding);
mb_internal_encoding($sys_encoding);

$sys_start_time = getmicrotime();

# fetch new login data
// TODO: sataisīt, lai automātiski izlogojas, ja ieķeksē neatktīvs
// TODO: sataisīt, lai !!!BEIDZOT!!! sessijas dati saglabātos korekti! (arī polls!)
/*
if($i_am_admin)
{
	if(isset($_SESSION['login']['l_login']))
	{
		$Login = new Logins;
		$_SESSION['login'] = $Login->load_by_login($_SESSION['login']['l_login']);
	}
}
*/

/* dabuujam parametrus no mod_rewrite */
$parts = explode('?', $_SERVER["REQUEST_URI"]);
$_SERVER["REQUEST_URI"] = isset($parts[0]) ? $parts[0] : '';
$_SERVER["QUERY_STRING"] = isset($parts[1]) ? $parts[1] : '';

preg_match_all("/\/([^\/]*)/i", $_SERVER["REQUEST_URI"], $matches);
$arr_base_dirs = explode('/', $sys_http_root);
$sys_parameters = array();
if (isset($matches[1]))
	foreach ($matches[1] as $match)
		if (!in_array($match, $arr_base_dirs))
			$sys_parameters[] = $match;

$sys_parameters = parse_params($sys_parameters);

$sys_module_id = array_shift($sys_parameters);
$sys_http_root_base = $sys_http_root;

// ja nav ne1 modulis selekteets
if(!$sys_module_id && $sys_first_module)
{
	$sys_module_id = $sys_first_module;
	//header('Location: '.$sys_http_root.'/'.$sys_first_module.'/');
	//exit;
}

$module_root = $sys_http_root.'/'.$sys_module_id;

// nochekojam, vai modulis existee, ja nee tad vai mappings iraid
if(isset($module_map[$sys_module_id]) && !file_exists('../modules/module.'.$sys_module_id.'.php'))
	$sys_module_id = $module_map[$sys_module_id];

$module = new Module;
$sys_modules = $module_tree = $module->load_tree(0);

$sys_module = !invalid($sys_module_id) &&
	(
		isset($sys_modules[$sys_module_id]) ||
		file_exists('../modules/module.'.$sys_module_id.'.php') ||
		isset($module_map[$sys_module_id])
	) ?
	$sys_module_id:
	(isset($sys_modules[$sys_default_module]) ? $sys_default_module : '');

$path = array();
$_pointer = $_pointer2 = &$module_tree[$sys_module_id];

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

/* iesleedzam vaidziigo moduli */
if(file_exists('../modules/module.'.$sys_module.'.php')) {
	include('../modules/module.'.$sys_module.'.php');
} else {
	include('../modules/module.'.$sys_default_module.'.php');
}

$my_login = new Logins;
$my_login->save_session_data();

/*
if($i_am_admin)
{
	$sys_end_time = getmicrotime();
	print 'Finished: '.number_format(($sys_end_time - $sys_start_time), 2, '.', '');
}
*/


