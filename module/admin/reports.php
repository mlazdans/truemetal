<?php

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: reporti');

$report = postget('report');

if($report == 'ip')
{
	include("module/admin/reports/ip.inc.php");
}

$template->out();

