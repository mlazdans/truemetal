<?php declare(strict_types = 1);

require_once('../include/boot.php');

set_time_limit(5);

register_shutdown_function("tm_shutdown");

# Bans
if(isset($sys_banned[$ip]))
{
	print "Banned: ".$sys_banned[$ip];
	return;
}

# Dabūjam parametrus no mod_rewrite
if(!isset($_SERVER["SERVER_PROTOCOL"]))$_SERVER["SERVER_PROTOCOL"] = "HTTP/1.0";
if(!isset($_SERVER["REQUEST_URI"]))$_SERVER["REQUEST_URI"] = "";

$sys_parameters = (function(): array {
	$parts = explode('?', $_SERVER["REQUEST_URI"], 2);
	if(isset($parts[0])){
		$path_segment = rawurldec($parts[0]);
		foreach(explode('/', $path_segment) as $k=>$v){
			if($v = trim($v)){
				$sys_parameters[$k] = $v;
			}
		}
	}

	# Šeit mums vajag dekodēt un arī pārrakstīt QUERY_STRING ērtākai izmantošanai
	$_SERVER["QUERY_STRING"] = isset($parts[1]) ? urldec($parts[1]) : "";

	return $sys_parameters??[];
})();

$sys_module_id = array_shift($sys_parameters);
if(file_exists("$sys_root/module/$sys_module_id.php"))
{
} elseif(isset($sys_module_map[$sys_module_id])) {
	$sys_module_id = $sys_module_map[$sys_module_id];
}

$module_root = "/".($sys_module_id ? $sys_module_id : $sys_default_module);

$_GET = _GET();

if($sys_debug)
{
	include("includer.php");
} else {
	try {
		include("includer.php");
	} catch(PDOException $e){
		$template = new MainTemplate;
		$template->error("Datubāzes kļūda. Ielogota un tiks apskatīta.");
		$template->print();
		throw $e;
	} catch(Throwable $e) {
		$template = new MainTemplate;
		$template->error("True Kļūda. Ielogota un tiks apskatīta.");
		$template->set_right_defaults();
		try {
			$template->set_right_defaults();
		} catch(Throwable $e) {
		}
		$template->print();
		throw $e;
	}
}
