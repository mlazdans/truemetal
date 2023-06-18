<?php declare(strict_types = 1);

if(!in_array($sys_module_id, $sys_nosess_modules)){
	require_once('include/session_handler.php');
}

if(file_exists("$sys_root/module/$sys_module_id.php")) {
	include("$sys_root/module/$sys_module_id.php");
} else {
	include("$sys_root/module/$sys_default_module.php");
}
