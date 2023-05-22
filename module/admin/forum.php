<?php declare(strict_types = 1);

use dqdp\Template;

function set_themes(Template $T, ViewResForumCollection $data, $d = 0) {
	if($d == 2)
		return 0;

	if(count($data))
		$T->enable('BLOCK_forum_themes');
	else
		$T->enable('BLOCK_forum_nothemes');

	foreach($data as $item)
	{
		$T->set_array($item);

		# ja aktiivs vai nee - kaukaa iekraaso to
		$T->disable('BLOCK_forum_inactive');
		$T->disable('BLOCK_forum_closed');
		if($item->res_visible) {
			$T->set_var('forum_color_class', 'box-normal');
		} else {
			$T->enable('BLOCK_forum_inactive');
			$T->set_var('forum_color_class', 'box-inactive');
		}

		if($item->forum_closed) {
			$T->enable('BLOCK_forum_closed');
			$T->set_var('forum_color_class', 'box-inactive');
		}

		$T->set_var('forum_padding', str_repeat('&nbsp;', 3 * $d));
		$T->parse_block('BLOCK_forum_theme_item', TMPL_APPEND);
	}
}

function root_forum(AdminModule $template): Template
{
	$T = $template->add_file("admin/forum.tpl");

	$F = new ResForumFilter(
		res_resid:false,
		res_visible:false,
	);

	$items = (new ViewResForumEntity)->getAll($F);

	set_themes($T, $items);

	# jauna teema
	$T->enable('BLOCK_forum_theme_new');

	return $T;
}

function open_forum(AdminModule $template, int $forum_id): ?Template
{
	$T = $template->add_file("admin/forum.tpl");

	$T->set_var('forum_id', $forum_id);

	$forum = (new ViewResForumEntity)->getById($forum_id, true);

	if(!$forum)
	{
		$template->not_found();
		return null;
	}

	if($forum->forum_allow_childs)
	{
		$F = (new ResForumFilter(
			res_resid:$forum->res_id,
			res_visible:false,
		))
		->rows(500)
		->orderBy("res_entered DESC");

		set_themes($T, (new ViewResForumEntity)->getAll($F));

		# jauna teema
		$T->enable('BLOCK_forum_theme_new');
	} else {

		$CF = (new ResCommentFilter(
			res_resid: $forum->res_id,
			res_visible:false,
		))->orderBy("res_entered DESC");

		$comments = (new ViewResCommentEntity)->getAll($CF);

		$C = new_template("admin/comment/list.tpl");

		admin_comment_list($C, $comments);

		$T->set_block_string($C->parse(), 'BLOCK_forum_comments');
	}

	$T->enable('BLOCK_forum_edit');

	$T->set_array($forum);
	$T->set_var('res_data', specialchars($forum->res_data));

	$T->set_var('res_visible'.$forum->res_visible, ' checked');
	$T->set_var('forum_closed'.$forum->forum_closed, ' checked');
	$T->set_var('forum_allow_childs'.$forum->forum_allow_childs, ' checked');

	foreach(Forum::$types as $type_id=>$type_name){
		$T->set_var('type_id', $type_id);
		$T->set_var('type_name', $type_name);
		$T->set_var('type_id_selected', '');
		$T->set_var('type_id_selected', $type_id == $forum->type_id ? 'selected' : '');
		$T->parse_block('BLOCK_forum_type_list', TMPL_APPEND);
	}

	// $module->set_modules_all($T, $forum->forum_modid, 'BLOCK_modules_under_list');

	$T->set_var("forum_display".$forum->forum_display, ' selected="selected"');

	return $T;
}


$forum_id = (int)array_shift($sys_parameters);
$action = post('action');

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide', 'comment_move')))
{
	if(include('module/admin/comment/action.inc.php')){
		header("Location: ".($forum_id ? "$module_root/$forum_id/" : "$module_root"));
	}
	return;
}

if(in_array($action, array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'move_multiple', 'close_multiple', 'open_multiple')))
{
	if($forum->process_action($_POST, $action, Res::ACT_VALIDATE))
	{
		if($forum_id)
			header("Location: $module_root/$forum_id/");
		else
			header("Location: $module_root/");
	}
	return;
}

if($action == 'save_forum') {
	if(isset($_POST['data'])) {
		if($forum->save($_POST['data'], Res::ACT_VALIDATE))
			if($forum_id)
				header("Location: $module_root/$forum_id/");
			else
				header("Location: $module_root/");
	}
	return;
}

if($action == 'add_forum') {
	if(isset($_POST['data'])) {
		$_POST['data']['forum_allowchilds'] = 'Y';
		$_POST['data']['forum_active'] = 'N';
		if($forum->add($forum_id, $_POST['data'], Res::ACT_VALIDATE, Res::STATE_ALL))
		{
			if($forum_id)
				header("Location: $module_root/$forum_id/");
			else
				header("Location: $module_root/");
		}
	}
	return;
}

$template = new AdminModule('forum');
$template->set_title('Admin :: forumi');

// $forum->set_forum_path($T, $forum_id);

# Forums atvērts
if($forum_id)
{
	$T = open_forum($template, $forum_id);
} else {
	$T = root_forum($template);
}

$template->out($T??null);
