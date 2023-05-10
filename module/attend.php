<?php declare(strict_types = 1);

$res_id = (int)array_shift($sys_parameters);
$off = array_shift($sys_parameters);

$template = new MainModule('attend');
$template->set_right_defaults();
$template->out(attend($template, $res_id, $off));
