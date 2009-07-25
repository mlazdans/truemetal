<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title('Video');
$template->set_file('FILE_video', 'tmpl.video.php');
$template->copy_block('BLOCK_middle', 'FILE_video');

$template->set_right();
$template->set_poll();
$template->set_online();
$template->out();
