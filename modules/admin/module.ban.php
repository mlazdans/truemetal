<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.Ban.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$ub_id = (integer)array_shift($sys_parameters);

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: bani');

$ban = new Ban;
if($ban->error_msg) {
	ban_error($ban->error_msg, $template);
	$template->out();
	exit;
}

/* ------------------------------------------------------------------------- */

function ban_error($msg, &$template) {
	$template->enable('BLOCK_ban_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');

if(in_array($action, $actions)) {
	if($ban->process_action($_POST, $action))
		if($ub_id)
			header("Location: $module_root/$ub_id/");
		else
			header("Location: $module_root/");
	exit;
}

/* cancel */
if($action == 'cancel') {
	header("Location: $module_root/");
	exit;
/* save */
} elseif($action == 'ub_save') {
	if($id = $ban->save($_POST['data'])) {
		header("Location: $module_root/".$id.'/');
		exit;
	} else
		ban_error($ban->error_msg, $template);
} elseif($action == 'ub_new') {
// jauns
	$template->enable('BLOCK_ban_edit');
	$template->set_var('ub_mask', '255.255.255.255');
	$ban->set_up_modules($template);
} else {
// saraksts
	if(!$ub_id) {
		$bans = $ban->load(0, '', BAN_ALL);

		if(count($bans))
			$template->enable('BLOCK_ban_list');

		$ban_count = 0;
		foreach($bans as $item) {
			++$ban_count;
			$template->set_var('ub_nr', $ban_count);
			$template->set_array($item);

			$template->set_var('ub_color_class', 'box-normal');

			if($item['ub_active'] == BAN_ACTIVE) {
				$template->set_var('ub_active_y', ' selected');
				$template->set_var('ub_color_class', 'box-normal');
			} else {
				$template->set_var('ub_active_n', ' selected');
				$template->set_var('ub_color_class', 'box-inactive');
			}

			$template->parse_block('BLOCK_bans', TMPL_APPEND);
		} // foreach bans
		$template->set_var('ub_count', $ban_count);
	} else {
// redigeet
		$item = $ban->load($ub_id, '', BAN_ALL);
		$template->enable('BLOCK_ban_edit');

		$template->set_array($item);

		if($item['ub_active'] == BAN_ACTIVE)
			$template->set_var('ub_active_y', ' selected');
		else
			$template->set_var('ub_active_n', ' selected');
		$ban->set_up_modules($template);
	}
} // action

/* ------------------------------------------------------------------------- */

$template->out();

?>