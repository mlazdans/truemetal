<?php declare(strict_types = 1);

function res_debug(MainTemplate $template, int $res_id): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!($res = ViewResEntity::get_by_id($res_id))){
		$template->not_found();
		return null;
	}

	if(!User::can_debug_res($res)){
		$template->forbidden("Nav tiesību skatīt");
		return null;
	}

	$T = new ResDebugTemplate;
	$T->res = $res;

	return $T;
}

$res_id = (int)array_shift($sys_parameters);

$template = new MainTemplate();
$template->set_right_defaults();
$template->MiddleBlock = res_debug($template, $res_id);
$template->print();
