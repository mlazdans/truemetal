<?php declare(strict_types = 1);

require_once('../include/boot.php');

# Bans
if(isset($sys_banned[$ip]))
{
	print "Banned: ".$sys_banned[$ip];
	return;
}

require_once('include/dbconnect.php');

# dabuujam parametrus no mod_rewrite
if(!isset($_SERVER["SERVER_PROTOCOL"]))
	$_SERVER["SERVER_PROTOCOL"] = "HTTP/1.0";
if(!isset($_SERVER["REQUEST_URI"]))
	$_SERVER["REQUEST_URI"] = "";

$parts = explode('?', $_SERVER["REQUEST_URI"]);
$_SERVER["REQUEST_URI"] = array_shift($parts);
$_SERVER["QUERY_STRING"] = join("?", $parts);

$sys_parameters = explode('/', $_SERVER["REQUEST_URI"]);
$sys_parameters = rawurldecode_params($sys_parameters);

$sys_module_id = array_shift($sys_parameters);
# ja nav ne1 modulis selekteets
if(!$sys_module_id && $sys_default_module)
	$sys_module_id = $sys_default_module;

if(!in_array($sys_module_id, $sys_nosess_modules)){
	require_once('include/session_handler.php');
}

register_shutdown_function("tm_shutdown");

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

$path = [];
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

if($i_am_admin)
{
	if(file_exists("$sys_root/module/$sys_module.php")) {
		include("$sys_root/module/$sys_module.php");
	} else {
		include("$sys_root/module/$sys_default_module.php");
	}
} else {
	try {
		if(file_exists("$sys_root/module/$sys_module.php")) {
			include("$sys_root/module/$sys_module.php");
		} else {
			include("$sys_root/module/$sys_default_module.php");
		}
	} catch(Throwable $e) {
		$template = new MainModule("error");
		$template->error("True Kļūda");
		$template->set_right_defaults();
		$template->out(null);
		throw $e;
	}
}
