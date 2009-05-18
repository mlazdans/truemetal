<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../classes/class.Module.php');
$module = new Module;
//$template = new Template($sys_template_root.'/admin', $admin_module);
$template = new Template($sys_template_root.'/admin/editor');
$template->set_file("FILE_index", 'tmpl.getmodule.php');

$template->set_var('encoding', $sys_encoding);
$template->set_var('http_root', $sys_http_root);
//$module->set_modules(&$template, 0);
//function set_modules(&$template, $mod_id = 0, $block = 'BLOCK_modules', $mod_modid = 0, $d = 0, $module_path = '', $cond = 'module_visible = "Y"') {
$module->set_modules_all(&$template);

print $template->parse_file('FILE_index');
?>