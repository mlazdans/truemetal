<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id);
$template->set_title('Paroles maiņa');

$template->set_right_defaults();
$template->out(change_pw($template));
