<?php declare(strict_types = 1);

$value = array_shift($sys_parameters);
$res_id = (int)array_shift($sys_parameters);

$template = new MainTemplate();
if($T = vote($template, $value, $res_id)){
	$T->print();
	return;
} else {
	# TODO: abstract out
	if(isset($_GET['json'])){
		header('Content-Type: text/javascript; charset=utf-8');

		$template->set_out('container');

		$jsonData = new StdClass;
		$jsonData->title = "[ TRUEMETAL ".specialchars($template->get_title())." ]";
		$jsonData->html = $template->parse();
		print json_encode($jsonData);
	} else {
		$template->set_right_defaults();
		$template->print();
	}
}
