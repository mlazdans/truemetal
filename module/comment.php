<?php declare(strict_types = 1);

$section = array_shift($sys_parameters);

function comment_edit(MainTemplate $template, int $c_id): ?AbstractTemplate
{
	if(!User::logged()){
		$template->not_logged();
		return null;
	}

	if(!($Comment = ViewResCommentEntity::get_by_id($c_id))){
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

if($section == 'edit')
{
	$template = new MainTemplate();
	$c_id = (int)array_shift($sys_parameters);
	$template->set_right_defaults();
	$template->MiddleBlock = comment_edit($template, $c_id);
	$template->print();
} else {
	redirect("/");
}
