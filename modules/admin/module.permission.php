<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.Permission.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$user_login = array_shift($sys_parameters);

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: tiesības');

$up = new Permission();
if($up->error_msg) {
	up_error($up->error_msg, $template);
	$template->out();
	exit;
}

/* ------------------------------------------------------------------------- */

function up_error($msg, &$template) {
	$template->enable('BLOCK_up_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple', 'save_multiple');
if(in_array($action, $actions)) {
	if($up->process_action($_POST, $action))
		if($user_login)
			header("Location: $module_root/$user_login/");
		else
			header("Location: $module_root/");
	exit;
}

if($action == 'delete_bylogins') {
	if($up->del_bylogins($_POST))
		header("Location: $module_root/");
	exit;
}

/* cancel */
if($action == 'cancel') {
	header("Location: $module_root/");
	exit;
/* save */
} elseif($action == 'up_save') {
	if($user_login = $up->save($_POST['data'])) {
		header("Location: $module_root/$user_login/");
		exit;
	} else
		up_error($up->error_msg, $template);
} elseif($action == 'up_new') {
// jauns
	$template->enable('BLOCK_up_edit');
	$template->enable('BLOCK_action');
	$template->enable('BLOCK_submit');
	$up->set_up_permissions($template);
	$up->set_up_modules($template);
	$up->set_up_users($template, $user_login);
	$template->set_var('up_userlogin', '0');
} else {
// saraksts
	if(!$user_login) {
		$ups = $up->load_users();

		if(count($ups))
			$template->enable('BLOCK_up_list');

		$up_count = 0;
		foreach($ups as $item) {
			++$up_count;
			$template->set_array($item);
			$template->set_var('up_nr', $up_count);
			$template->parse_block('BLOCK_ups', TMPL_APPEND);
		} // foreach user permissions
		$template->set_var('up_count', $up_count);
	} else {
// redigeet
		$permissons = $up->load(0, $user_login);
		if(!count($permissons)) {
			header("Location: $module_root/");
			exit;
		}
		$template->enable('BLOCK_up_edit');
		$template->enable('BLOCK_action_list');
		$perm_count = 0;
		foreach($permissons as $item) {
			++$perm_count;
			$template->set_var('perm_nr', $perm_count);
			$template->set_array($item);
			$up->set_up_permissions($template, $item['up_permissions']);
			$up->set_up_modules($template);
			$up->set_up_users($template, $item['user_login']);
			$template->parse_block('BLOCK_up_edit_list', TMPL_APPEND);
		}
	}
} // action

/* ------------------------------------------------------------------------- */

$template->out();

?>