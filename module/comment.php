<?php declare(strict_types = 1);

use dqdp\Template;

$section = array_shift($sys_parameters);

function comment_edit(MainModule $template, int $c_id): ?Template
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!($Comment = ViewResCommentEntity::getById($c_id))){
		$template->not_found();
		return null;
	}

	if(!User::can_edit_comment($Comment)){
		$template->forbidden("Nav tiesību labot");
		return null;
	}

	$T = $template->add_file("comment_edit_form.tpl");
	$T->enable('BLOCK_comment_edit_form');

	$action = post('action');

	$error_msg = [];
	if($action == 'update_comment')
	{
		if(update_comment($template, $T, $Comment->res_id, post('res_data'), $error_msg)){
			redirect($Comment->res_route);
			return null;
		}
	} else {
		$T->set_var('res_data', htmlspecialchars($Comment->res_data));
	}

	$T->set_var('res_route', $Comment->res_route);

	if($error_msg)
	{
		$T->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	return $T;
}

if($section == 'edit')
{
	$template = new MainModule("comment");
	$c_id = (int)array_shift($sys_parameters);
	$template->set_right_defaults();
	$template->out(comment_edit($template, $c_id));
} else {
	redirect("/");
}
