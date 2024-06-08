<?php declare(strict_types = 1);

$value = array_shift($sys_parameters);
$res_id = (int)array_shift($sys_parameters);

$template = new MainTemplate();
if($T = vote($template, $value, $res_id)){
	$T->print();
	return;
}
