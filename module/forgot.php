<?php declare(strict_types = 1);

$action = array_shift($sys_parameters);

$template = new MainTemplate($sys_module_id);
$template->set_title("Aizmirsu paroli");

if($action == 'accept') {
	$code = array_shift($sys_parameters);
	$T = forgot_accept($template, $code);
} else {
	$T = forgot($template);
}

$template->set_right_defaults();
$template->MiddleBlock = $T;
$template->print();
