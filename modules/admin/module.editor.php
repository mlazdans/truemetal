<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$editor_module = array_shift($sys_parameters);

if(!$editor_module)
	header("Location: $admin_root/modules/");
else {
	if(file_exists('../modules/admin/editor/module.'.$editor_module.'.php')) {
		include('../modules/admin/editor/module.'.$editor_module.'.php');
	}
}

?>