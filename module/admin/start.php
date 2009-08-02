<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: hello');
$template->out();
