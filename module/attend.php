<?php declare(strict_types = 1);

# TODO: kādreiz atdalīt pasākumus savā klasē
$res_hash = array_shift($sys_parameters);
$off = array_shift($sys_parameters);
$get = isset($_GET['get']);

$template = new MainTemplate();

if(!User::logged()){
	$template->forbidden();
	$template->print();
	return;
}

if($get)
{
	if($forum = ViewResForumEntity::get_by_hash($res_hash))
	{
		$template->MiddleBlock = attendees_view($forum);

		# TODO: abstract out
		header('Content-Type: text/javascript; charset=utf-8');

		$template->set_out('container');

		$jsonData = new StdClass;
		$jsonData->title = "[ TRUEMETAL ".specialchars($template->get_title())." ]";
		$jsonData->html = $template->parse();
		print json_encode($jsonData);
	} else {
		$template->not_found();
		$template->print();
	}
} else {
	if($R = attend($template, $res_hash, $off)){
		$R->print();
	} else {
		$template->set_right_defaults();
		$template->print();
	}
}
