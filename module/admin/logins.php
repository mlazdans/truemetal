<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$l_id = (int)array_shift($sys_parameters);
$action = post('action');

require_once('lib/Logins.php');
require_once('lib/Module.php');
require_once('lib/Article.php');
require_once('lib/Forum.php');

$logins = new Logins;
$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: logini');

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');
if(in_array($action, $actions))
{
	if($logins->process_action($_POST, $action))
	{
		if(empty($p_id))
			header("Location: $module_root/");
		else
			header("Location: $module_root/$p_id/");
	}
	return;
}

if($l_id)
{
	include("module/admin/logins/edit.inc.php");
} else {
	include("module/admin/logins/list.inc.php");
}

$template->out();

