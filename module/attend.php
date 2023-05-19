<?php declare(strict_types = 1);

# TODO: kādreiz atdalīt pasākumus savā klasē
$res_id = (int)array_shift($sys_parameters);
$off = array_shift($sys_parameters);
$get = isset($_GET['get']);

$template = new MainModule('attend');

if($get){
	if($forum = ViewResForumEntity::getByResId($res_id))
	{
		$T = attendees($template, $forum);
	}
	$template->out($T??null);
} else {
	$template->set_right_defaults();
	$template->out(attend($template, $res_id, $off));
}
