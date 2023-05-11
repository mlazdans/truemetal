<?php declare(strict_types = 1);

$res_id = (int)array_shift($sys_parameters);
$off = array_shift($sys_parameters);
$get = isset($_GET['get']);

$template = new MainModule('attend');

if($get){
	$template->out(attendees($template, $res_id));
} else {
	$template->set_right_defaults();
	$template->out(attend($template, $res_id, $off));
}
