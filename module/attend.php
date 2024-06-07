<?php declare(strict_types = 1);

# TODO: kādreiz atdalīt pasākumus savā klasē
$res_id = (int)array_shift($sys_parameters);
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
	if($forum = ViewResForumEntity::get_by_res_id($res_id))
	{
		$T = attendees_view($forum);
		$T->print();
	} else {
		$template->not_found();
		$template->print();
	}
} else {
	attend($template, $res_id, $off);
	$template->set_right_defaults();
	$template->print();
}
