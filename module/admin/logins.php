<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$l_id = (int)array_shift($sys_parameters);
$action = postget('action');

require_once('lib/Logins.php');
require_once('lib/Module.php');
require_once('lib/Article.php');
require_once('lib/Forum.php');

$logins = new Logins;

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');
if(in_array($action, $actions))
{
	if($logins->process_action($_POST, $action))
		header("Location: $module_root/");

	return;
}

if($action == 'save')
{
	if($logins->update($_POST['data'], LOGIN_DONTVALIDATE))
		header("Location: $module_root/".($l_id ? "$l_id/" : ""));
	else
		print $logins->error_msg;

	return;
}

if($l_id)
{
	include("module/admin/logins/edit.inc.php");
} else {
	include("module/admin/logins/list.inc.php");
}

