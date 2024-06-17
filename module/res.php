<?php declare(strict_types = 1);

function res_debug(MainTemplate $template, ?ResourceTypeInterface $res): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
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

function comment_edit(MainTemplate $template, ?ViewResCommentType $Comment): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!$Comment){
		$template->not_found();
		return null;
	}

	if(!User::can_edit_res($Comment)){
		$template->forbidden("Nav tiesību labot");
		return null;
	}

	$action = post('action');
	$error_msg = [];

	$T = new CommentEditFormTemplate;
	$T->l_nick = User::nick();

	if($action == 'update_comment')
	{
		if(update_comment($template, $Comment->res_id, post('res_data'), $error_msg)){
			redirect($Comment->res_route);
			return null;
		}
	} else {
		$T->res_data = specialchars($Comment->res_data);
	}

	$T->res_route = $Comment->res_route;

	if($error_msg)
	{
		$T->error_msg = join("<br>", $error_msg);
	}

	return $T;
}

function res_route(MainTemplate $template, ?ResourceTypeInterface $res): ?AbstractTemplate
{
	if(!User::is_admin()){
		$template->forbidden();
		return null;
	}

	$moved = false;
	if($redirect_res = ResRedirectEntity::get_by_from_res_id($res->res_id)){
		$moved = true;
		$res = load_res($redirect_res->to_res_id);
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
	}

	return null;
}

function process_request(MainTemplate $template): ?AbstractTemplate
{
	global $sys_parameters;

	$section = array_shift($sys_parameters);
	$res_hash = array_shift($sys_parameters);

	if(!$section || !$res_hash){
		$template->bad_request();
		return null;
	}

	if(!($res = load_res_by_hash($res_hash))){
		$template->not_found();
		return null;
	}

	if($section == 'debug'){
		return res_debug($template, $res);
	} elseif($section == 'edit') {
		if($res instanceof ViewResCommentType){
			return comment_edit($template, $res);
		}
	} elseif($section == 'route'){
		return res_route($template, $res);
	}

	$template->bad_request();

	return null;
}

$template = new MainTemplate();
$template->set_right_defaults();
$template->MiddleBlock = process_request($template);
$template->print();
