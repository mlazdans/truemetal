<?php declare(strict_types = 1);

use dqdp\Template;

function set_themes(Template $T, &$data, $d = 0) {
	global $forum;

	if($d == 2)
		return 0;

	if(count($data))
		$T->enable('BLOCK_forum_themes');
	else
		$T->enable('BLOCK_forum_nothemes');

	foreach($data as $item) {
		$T->set_var('forum_id', $item['forum_id'], 'BLOCK_forum_theme_item');
		$T->set_var('res_comment_count', $item['res_comment_count'], 'BLOCK_forum_theme_item');
		$T->set_var('forum_name', $item['forum_name'], 'BLOCK_forum_theme_item');

		# ja aktiivs vai nee - kaukaa iekraaso to
		$T->disable('BLOCK_forum_inactive');
		$T->disable('BLOCK_forum_closed');
		if($item['forum_active'] == Res::STATE_ACTIVE) {
			$T->set_var('forum_color_class', 'box-normal', 'BLOCK_forum_theme_item');
		} else {
			$T->enable('BLOCK_forum_inactive');
			$T->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum_theme_item');
		}

		if($item['forum_closed'] == Forum::CLOSED) {
			$T->enable('BLOCK_forum_closed');
			$T->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum_theme_item');
		}

		$T->set_var('forum_padding', str_repeat('&nbsp;', 3 * $d));
		$T->parse_block('BLOCK_forum_theme_item', TMPL_APPEND);
	}
}

$forum = new Forum();
$module = new Module();

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
$T = $template->add_file("admin/forum.tpl");

$T->set_var('forum_id', $forum_id);
$forum->set_forum_path($T, $forum_id);

$items = $forum->load(array(
	'forum_forumid'=>$forum_id,
	'forum_active'=>Res::STATE_ALL,
	));

# Forums atvērts
if($forum_id)
{
	$forum_data = $forum->load(array(
		'forum_id'=>$forum_id,
		'forum_active'=>Res::STATE_ALL,
		));

	if($forum_data['forum_allowchilds'] == Forum::ALLOW_CHILDS)
	{
		set_themes($T, $items);
		# jauna teema
		$T->enable('BLOCK_forum_theme_new');
	} else {
		$T->enable('BLOCK_forum_resid');

		$RC = new ResComment();
		$comments = $RC->Get(array(
			'res_id'=>$forum_data['res_id'],
			'c_visible'=>Res::STATE_ALL,
		));

		$C = new_template("admin/comment/list.tpl");
		admin_comment_list($C, $comments);

		$T->set_block_string('BLOCK_forum_comments', $C->parse());
	}

	$T->enable('BLOCK_forum_edit');

	$T->set_array($forum_data, 'BLOCK_forum_edit');
	$T->set_var('forum_data', specialchars($forum_data['forum_data']), 'BLOCK_forum_edit');

	if($forum_data['forum_active'] == Res::STATE_ACTIVE)
	{
		$T->set_var('forum_active_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$T->set_var('forum_inactive_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_closed'] == Forum::CLOSED)
	{
		$T->set_var('forum_closed_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$T->set_var('forum_open_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_allowchilds'] == Forum::ALLOW_CHILDS)
	{
		$T->set_var('forum_allowchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$T->set_var('forum_prohibitchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	foreach($forum->types as $type_id=>$type_name){
		$T->set_var('type_id', $type_id, 'BLOCK_forum_type_list');
		$T->set_var('type_name', $type_name, 'BLOCK_forum_type_list');
		$T->set_var('type_id_selected', '', 'BLOCK_forum_type_list');
		if($type_id == $forum_data['type_id']){
			$T->set_var('type_id_selected', ' selected="selected"', 'BLOCK_forum_type_list');
		}
		$T->parse_block('BLOCK_forum_type_list', TMPL_APPEND);
	}

	$module->set_modules_all($T, $forum_data['forum_modid'], 'BLOCK_modules_under_list');
	$T->set_var("forum_display_$forum_data[forum_display]_selected", ' selected="selected"', 'BLOCK_forum_edit');
} else {
	# Root
	set_themes($T, $items);

	# jauna teema
	$T->enable('BLOCK_forum_theme_new');
}

$template->out($T);
