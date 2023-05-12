<?php declare(strict_types = 1);

$l_id = (int)array_shift($sys_parameters);
$action = postget('action');
$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');
$logins = new Logins;

if(in_array($action, $actions))
{
	if($logins->process_action($_POST, $action)){
		redirect_referer();
		// header("Location: $module_root/");
	}

	return;
}

if($action == 'save')
{
	if($logins->update($_POST['data'], Res::ACT_DONTVALIDATE))
		header("Location: $module_root/".($l_id ? "$l_id/" : ""));
	else
		print join("<br>", $logins->error_msg);

	return;
}

// XXX: ja post tukšs
$action = get('action');

if($l_id)
{
	include('module/admin/logins/edit.inc.php');
} else {
	include('module/admin/logins/list.inc.php');
}

