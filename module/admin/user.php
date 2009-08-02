<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('lib/User.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$user_login = array_shift($sys_parameters);

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: lietotāji');

$user = new User();
if($user->error_msg) {
	user_err($user->error_msg, $template);
	$template->out();
	exit;
}

/* ------------------------------------------------------------------------- */

function user_err($msg, &$template) {
	$template->enable('BLOCK_user_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');

if(in_array($action, $actions)) {
	if($user->process_action($_POST, $action))
		if($user_login)
			header("Location: $module_root/$user_login/");
		else
			header("Location: $module_root/");
	exit;
}

/* cancel */
if($action == 'cancel') {
	header("Location: $module_root/");
	exit;
/* save */
} elseif($action == 'user_save') {
	if($id = $user->save($user_login, $_POST['data'])) {
		header("Location: $module_root/".$id.'/');
		exit;
	} else
		user_err($user->error_msg, $template);
} elseif($action == 'user_new') {
// jauns
	$template->enable('BLOCK_user_edit');
} else {
// saraksts
	if(!$user_login) {
		$users = $user->load('', '', USER_ALL);

		if(count($users))
			$template->enable('BLOCK_user_list');

		$user_count = 0;
		foreach($users as $item) {
			++$user_count;
			$template->set_var('user_nr', $user_count);
			$template->set_array($item);

			$template->set_var('user_color_class', 'box-normal');

			if($item['user_active'] == USER_ACTIVE) {
				$template->set_var('user_active_y', ' selected');
				$template->set_var('user_color_class', 'box-normal');
			} else {
				$template->set_var('user_active_n', ' selected');
				$template->set_var('user_color_class', 'box-inactive');
			}

			$template->parse_block('BLOCK_users', TMPL_APPEND);
		} // foreach users
		$template->set_var('user_count', $user_count);
	} else {
// redigeet
		$usr = $user->load($user_login, '', USER_ALL);
		$template->enable('BLOCK_user_edit');

		$template->set_array($usr);

		if($usr['user_active'] == USER_ACTIVE)
			$template->set_var('user_active_y', ' selected');
		else
			$template->set_var('user_active_n', ' selected');
	}
} // action

/* ------------------------------------------------------------------------- */

$template->out();
