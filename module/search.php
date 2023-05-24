<?php declare(strict_types = 1);

# TODO: generalize
$DOC_SOURCES = [
	1=>['name'=>"Jaunumi"],
	2=>['name'=>"Recenzijas"],
	3=>['name'=>"Intervijas"],
	4=>['name'=>"Forums"],
];

$section = array_shift($sys_parameters);

$template = new MainModule('search');
$template->set_descr("Metāliskais meklētājs");

if($section == 'log'){
	$T = search_log($template);
} else {
	$err_msg = [];
	$T = search($template, $DOC_SOURCES, $err_msg);

	if($err_msg){
		$template->error($err_msg);
	}
}

$template->set_right_defaults();
$template->out($T);
