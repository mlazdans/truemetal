<?php declare(strict_types = 1);

$template = new MainTemplate($sys_module_id);
$template->set_title("Logo");
$template->set_right_defaults();
$template->MiddleBlock = new LogoTemplate;
$template->print();
