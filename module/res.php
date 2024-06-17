<?php declare(strict_types = 1);

function res_debug(MainTemplate $template, ?ViewResType $res): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(empty($res)){
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

function process_request(MainTemplate $template): ?AbstractTemplate
{
	global $sys_parameters;

	$section = array_shift($sys_parameters);
	$res_hash = array_shift($sys_parameters);

	if(!$section || !$res_hash){
		$template->bad_request();
		return null;
	}

	if(!($res = ViewResEntity::get_by_hash($res_hash))){
		$template->not_found();
		return null;
	}

	if($section == 'debug'){
		return res_debug($template, $res);
	}

	if($section == 'edit') {
		if($res->res_kind == ResKind::COMMENT){
			return comment_edit($template, ViewResCommentEntity::get_by_res_id($res->res_id));
		}
	}

	$template->bad_request();

	return null;
}

$template = new MainTemplate();
$template->set_right_defaults();
$template->MiddleBlock = process_request($template);
$template->print();
