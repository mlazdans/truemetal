<?php declare(strict_types = 1);

use dqdp\TODO;

$l_id = (int)array_shift($sys_parameters);
$action = postget('action');

$template = new AdminModule("logins");

if(in_array($action, ['delete_multiple', 'activate_multiple', 'deactivate_multiple']))
{
	$logins = new Logins;
	if($logins->process_action($_POST, $action)){
		redirect_referer();
	}

	return;
}

if($action == 'save')
{
	new TODO("save");
	$logins = new Logins;
	if($logins->update($_POST['data'], Res::ACT_DONTVALIDATE))
		header("Location: $module_root/".($l_id ? "$l_id/" : ""));
	else
		print join("<br>", $logins->error_msg);

	return;
}

if($l_id)
{
	$T = admin_logins_edit($template, $l_id);
} else {
	$T = admin_logins_list($template);
}

$template->out($T);