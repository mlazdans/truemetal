<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id);
$template->set_title('Video');
$T = $template->add_file('video.tpl');

$template->set_right_defaults();
$template->out($T);
