<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title('Linki');
$template->set_file('FILE_linki', 'tmpl.links.php');
$template->copy_block('BLOCK_middle', 'FILE_linki');

$template->set_right();
$template->set_poll();
$template->set_online();
$template->out();
