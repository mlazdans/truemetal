<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$action = isset($_POST['action']) ? $_POST['action'] : '';

$l_id = array_shift($sys_parameters);

require_once('lib/Logins.php');
require_once('lib/Module.php');
require_once('lib/Article.php');
require_once('lib/Forum.php');

$logins = new Logins;
$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: logini');

/* ------------------------------------------------------------------------- */

function logins_error($msg, &$template) {
	$template->enable('BLOCK_logins_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');

if(in_array($action, $actions)) {
	if($logins->process_action($_POST, $action))
		if(!empty($p_id))
			header("Location: $module_root/$p_id/");
		else
			header("Location: $module_root/");
	exit;
}

if($l_id)
{
	include("module/admin/logins/edit.inc.php");
} else {
	include("module/admin/logins/list.inc.php");
}

$template->out();

