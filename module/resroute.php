<?php declare(strict_types = 1);

$template = new MainTemplate();

if(!User::is_admin()){
	$template->forbidden();
	$template->set_right_defaults();
	$template->print();
	return;
}

$res_id = (int)array_shift($sys_parameters);
$moved = false;

if(!($res = load_res($res_id))){
	if($redirect_res = DB::ExecuteSingle("SELECT * FROM res_redirect WHERE from_res_id = ?", $res_id)){
		$moved = true;
		$res = load_res($redirect_res['to_res_id']);
		$c_id = 0;
	}
}

if($res && ($location = $res->Route()))
{
	if($moved){
		redirectp($location);
	} else {
		redirect($location);
	}
} else {
	$template->not_found();
	$template->set_right_defaults();
	$template->print();
}
