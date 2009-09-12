<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title("Logo");
$template->set_file('FILE_logo', 'logo.tpl');
$template->copy_block('BLOCK_middle', 'FILE_logo');

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_search();

$template->out();
