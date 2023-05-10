<?php declare(strict_types = 1);

$value = array_shift($sys_parameters);
$res_id = (int)array_shift($sys_parameters);

$template = new MainModule('vote');
$template->set_right_defaults();
$template->out(vote($template, $value, $res_id));
