<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id);
$template->set_title('E-pasta maiņa');

$template->set_right_defaults();
$template->out(change_email($template));
