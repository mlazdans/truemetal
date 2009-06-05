<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title("Logo");
$template->set_file('FILE_logo', 'tmpl.logo.php');
$template->copy_block('BLOCK_middle', 'FILE_logo');

$template->set_right();
$template->set_login();
$template->set_poll();
$template->set_search();
$template->set_online();
$template->set_calendar();

$template->out();
