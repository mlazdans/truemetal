<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id);
$template->set_title("Logo");
$template->set_right_defaults();
$template->out($template->add_file('logo.tpl'));
