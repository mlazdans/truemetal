<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.Module.php');
require_once('../classes/class.MainModule.php');

function set_map(&$template, &$modules, $d = 0, $path = '')
{
	if(is_array($modules) && count($modules)) {
		foreach($modules as $module) {
			$item = &$module['_data_'];
			if($item['module_active'] == MOD_ACTIVE && $item['module_visible'] == MOD_VISIBLE) {
				$new_path = $path.'/'.$item['module_id'];
				$template->set_var('map_padding', $d * 18 + 21);
				$template->set_var('map_type', ($d ? 'sub' : 'root'));
				$template->set_var('map_module_name', $item['module_name']);
				$template->set_var('map_path', $new_path);
				$template->parse_block('BLOCK_mapitem', TMPL_APPEND);
				set_map($template, $module, $d + 1, $new_path);
			}
		} // loop
	} // is_array
}

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_title($sys_lang_def['page_map']);
$template->set_file('FILE_map', 'tmpl.map.php');
$template->copy_block('BLOCK_middle', 'FILE_map');

$path = array('map'=>array('module_id'=>'map', 'module_name'=>'LAPAS KARTE'));

set_parts($template);
$template->set_label($path);
$template->set_right($sys_modules, $sys_modules);
$template->set_modules($sys_modules);
$template->set_submodules($_pointer2);
$template->set_poll();
$template->set_login();
$template->set_calendar();
set_map($template, $sys_modules);

$template->out();

?>