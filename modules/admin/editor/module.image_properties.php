<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

//require_once('../classes/class.Module.php');
//$template = new Template($sys_template_root.'/admin', $admin_module);
$template = new Template($sys_template_root.'/admin/editor');
$template->set_file("FILE_index", 'tmpl.image_properties.php');

$template->set_var('encoding', $sys_encoding);
$template->set_var('http_root', $sys_http_root);

print $template->parse_file('FILE_index');

?>