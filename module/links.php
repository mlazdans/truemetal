<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new MainModule($sys_module_id);
$template->set_title('Linki');
$template->set_file('FILE_linki', 'links.tpl');
$template->copy_block('BLOCK_middle', 'FILE_linki');

$template->set_right_defaults();
$template->out();
