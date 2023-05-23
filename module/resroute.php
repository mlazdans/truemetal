<?php declare(strict_types = 1);

# TODO: vispār izvākt šo te moduli
$res_id = (int)array_shift($sys_parameters);
$c_id = (int)get('c_id');
$moved = false;

if(!($res = load_res($res_id))){
	if($redirect_res = DB::ExecuteSingle("SELECT * FROM res_redirect WHERE from_res_id = ?", $res_id)){
		$moved = true;
		$res = $res = load_res($redirect_res['to_res_id']);
		$c_id = 0;
	}
}

if($res)
{
	$location = $res->Route($c_id ? $c_id : null);
	if($moved){
		redirectp($location);
	} else {
		redirect($location);
	}
} else {
	$template = new MainModule("resroute");
	$template->not_found();
	$template->set_right_defaults();
	$template->out(null);
}
