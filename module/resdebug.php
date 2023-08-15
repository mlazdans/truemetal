<?php declare(strict_types = 1);

use dqdp\Template;

function res_debug(MainModule $template, int $res_id): ?Template
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!($Res = ViewResEntity::getById($res_id))){
		$template->not_found();
		return null;
	}

	if(!User::can_debug_res($Res)){
		$template->forbidden("Nav tiesību skatīt");
		return null;
	}

	ob_start();
	printr($Res);
	$res_debug = ob_get_clean();

	$T = $template->add_file("res_debug.tpl");

	$T->set_var('res_debug', $res_debug);

	return $T;
}

$res_id = (int)array_shift($sys_parameters);

$template = new MainModule("resdebug");
$template->set_right_defaults();
$template->out(res_debug($template, $res_id));
