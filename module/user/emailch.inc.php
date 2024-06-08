<?php declare(strict_types = 1);

$template = new MainTemplate($sys_module_id);
$template->set_title('E-pasta maiņa');

$template->set_right_defaults();
$template->MiddleBlock = change_email($template);
$template->print();
