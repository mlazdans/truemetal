<?php declare(strict_types = 1);

# TODO: vispār izvākt šo te moduli
$res_id = (int)array_shift($sys_parameters);
$c_id = (int)get('c_id');

if($res = Res::GetAll($res_id))
{
	$location = $res->Route($c_id ? $c_id : null);
	header("Location: $location");
} else {
	$template = new MainModule("resroute");
	$template->not_found();
	$template->set_right_defaults();
	$template->out(null);
}
