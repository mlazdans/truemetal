<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$hl = urldecode(join('', $sys_parameters));
preg_match('/hl=([^&]*)/i', $hl, $m);
$hl = isset($m[1]) ? $m[1] : '';

require_once('../classes/class.Module.php');
require_once('../classes/class.MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_title($_pointer['_data_']['module_name']);

if($hl)
	hl($_pointer['_data_']['module_data'], $hl);

$template->set_block_string('BLOCK_middle', $_pointer['_data_']['module_data']);

// kontakti
//if(isset($sys_modules['_contacts']['_data_']['module_data']))
//	$template->set_var('contacts', $sys_modules['_contacts']['_data_']['module_data']);

// cat name sarkanajaa raamii
if($_pointer['_data_']['module_name'])
	$template->set_label(toupper($_pointer['_data_']['module_name']));

$template->set_right($sys_modules, $_pointer);
$template->set_modules($sys_modules);
//$template->set_submodules($sys_modules);

$template->out();

?>