<?php declare(strict_types = 1);

require_once('../include/boot.php');

register_shutdown_function("tm_shutdown");

# Bans
if(isset($sys_banned[$ip]))
{
	print "Banned: ".$sys_banned[$ip];
	return;
}

require_once('include/dbconnect.php');

# dabuujam parametrus no mod_rewrite
if(!isset($_SERVER["SERVER_PROTOCOL"]))$_SERVER["SERVER_PROTOCOL"] = "HTTP/1.0";
if(!isset($_SERVER["REQUEST_URI"]))$_SERVER["REQUEST_URI"] = "";

(function(){
	$parts = explode('?', $_SERVER["REQUEST_URI"], 2);
	if(isset($parts[0]))$_SERVER["REQUEST_URI"] = rawurldec($parts[0]);
	if(isset($parts[1]))$_SERVER["QUERY_STRING"] = urldec($parts[1]);
})();

$sys_parameters = [];
foreach(explode('/', $_SERVER["REQUEST_URI"]) as $k=>$v){
	if($v2 = trim($v)){
		$sys_parameters[$k] = $v;
	}
}

$sys_module_id = array_shift($sys_parameters);

if(!$sys_module_id && $sys_default_module)
{
	$sys_module_id = $sys_default_module;
}

if(file_exists("$sys_root/module/$sys_module_id.php"))
{
} elseif(isset($sys_module_map[$sys_module_id])) {
	$sys_module_id = $sys_module_map[$sys_module_id];
}

if(!in_array($sys_module_id, $sys_nosess_modules)){
	require_once('include/session_handler.php');
}

$module_root = "/$sys_module_id";

$_GET = _GET();

header('Content-Type: text/html; charset='.$sys_encoding);
header('X-Powered-By: TRUEMETAL');

if($i_am_admin)
{
	if(file_exists("$sys_root/module/$sys_module_id.php")) {
		include("$sys_root/module/$sys_module_id.php");
	} else {
		include("$sys_root/module/$sys_default_module.php");
	}
} else {
	try {
		if(file_exists("$sys_root/module/$sys_module_id.php")) {
			include("$sys_root/module/$sys_module_id.php");
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
