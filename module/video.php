<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new MainModule($sys_module_id);
$template->set_title('Video');
$template->set_file('FILE_video', 'video.tpl');
$template->copy_block('BLOCK_middle', 'FILE_video');

$template->set_right_defaults();
$template->out();
