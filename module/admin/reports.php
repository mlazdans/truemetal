<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: reporti');

$report = postget('report');
$action = post('action');

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include('module/admin/comment/action.inc.php')){
		redirect();
	}
	return;
}

if($report == 'ip')
{
	include('module/admin/reports/ip.inc.php');
}

$template->out();

