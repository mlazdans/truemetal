<?php declare(strict_types = 1);

function set_pos(&$template, $m_pos = 0, $mod_modid = '') {
	$mod_modid = (integer)$mod_modid;

	if($mod_modid)
		$sql = "SELECT MAX(module_pos) module_pos FROM modules WHERE mod_modid = $mod_modid";
	else
		$sql = "SELECT MAX(module_pos) module_pos FROM modules WHERE mod_modid = 0";

	$data = DB::ExecuteSingle($sql);
	$module_pos = $data['module_pos'];
	$r = 1;
	for($r = 1; $r <= $module_pos; ++$r) {
		$template->set_var('pos', $r);
		$template->set_var('pos_name', $r);
		if($m_pos == $r)
			$template->set_var('pos_selected', ' selected="selected"');
		else
			$template->set_var('pos_selected', '');
		$template->parse_block('BLOCK_modules_pos', TMPL_APPEND);
	}
	if(!$m_pos) {
		$template->set_var('pos', $r);
		$template->set_var('pos_name', 'Nākamais ['.$r.']');
		$template->set_var('pos_selected', ' selected="selected"');
		$template->parse_block('BLOCK_modules_pos', TMPL_APPEND);
	}
}

function module_error($msg, &$template) {
	$template->enable('BLOCK_modules_error');
	$template->set_var('error_msg', $msg);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$mod_id = array_shift($sys_parameters);

require_once('lib/Module.php');

$module = new Module();
$template = new AdminModule($admin_module);
$template->set_title('Admin :: moduļi');

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'show_multiple', 'hide_multiple');

if(in_array($action, $actions)) {
	if($module->process_action($_POST, $action))
		if($mod_id)
			header("Location: $module_root/$mod_id/");
		else
			header("Location: $module_root/");
	exit;
}

if($action == 'cancel') {
	# atcelt
	header("Location: $module_root/");
	exit;
} elseif($action == 'module_save') {
	# saglabaat
	if($id = $module->save($_POST['data'])) {
		header("Location: $module_root/".$id.'/');
		exit;
	} else
		module_error($module->error_msg, $template);
} elseif($action == 'module_new') {
	# jauns modulis
	$template->enable('BLOCK_modules_edit');
	$template->set_var('module_name_edit', 'jauns', 'BLOCK_module_edit');
	set_pos($template, 0, 0);
} else {
	if(!$mod_id)
	{
		$template->enable('BLOCK_modules_list');
		unset($module_count);
		$module->set_modules_all($template);
	} else {
		$module->load();
		if(isset($module->data[$mod_id]))
		{
			# redigeeshana
			$template->enable('BLOCK_modules_edit');
			set_pos($template, $module->data[$mod_id]['module_pos'], $module->data[$mod_id]['mod_modid']);
			if($item = $module->find($module->data[$mod_id]['mod_modid'])) {
				$template->set_var('module_name_edit', $item['module_name'], 'BLOCK_module_edit');
				$template->set_var('module_module_name', $item['module_name'], 'BLOCK_module_edit');
				$template->set_var('module_module_id', $item['module_id'], 'BLOCK_module_edit');
				$template->set_var('module_mod_id', $item['mod_id'], 'BLOCK_module_edit');
			}
			$module->set_module($template, $module->data[$mod_id]);
		}
	}
}

$template->out();

