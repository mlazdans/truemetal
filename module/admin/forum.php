<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

function set_themes(&$template, &$data, $d = 0) {
	global $forum;

	if($d == 2)
		return 0;

	if(count($data))
		$template->enable('BLOCK_forum_themes');
	else
		$template->enable('BLOCK_forum_nothemes');

	foreach($data as $item) {
		$template->set_var('forum_id', $item['forum_id'], 'BLOCK_forum_theme_item');
		$template->set_var('res_comment_count', $item['res_comment_count'], 'BLOCK_forum_theme_item');
		$template->set_var('forum_name', $item['forum_name'], 'BLOCK_forum_theme_item');

		# ja aktiivs vai nee - kaukaa iekraaso to
		$template->disable('BLOCK_forum_inactive');
		$template->disable('BLOCK_forum_closed');
		if($item['forum_active'] == Res::STATE_ACTIVE) {
			$template->set_var('forum_color_class', 'box-normal', 'BLOCK_forum_theme_item');
		} else {
			$template->enable('BLOCK_forum_inactive');
			$template->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum_theme_item');
		}

		if($item['forum_closed'] == Forum::CLOSED) {
			$template->enable('BLOCK_forum_closed');
			$template->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum_theme_item');
		}

		$template->set_var('forum_padding', str_repeat('&nbsp;', 3 * $d));
		$template->parse_block('BLOCK_forum_theme_item', TMPL_APPEND);
	}
}

require_once('lib/AdminModule.php');
require_once('lib/Forum.php');
require_once('lib/Module.php');
require_once('lib/Comment.php');
require_once('lib/ResComment.php');

$forum = new Forum();
$module = new Module();

$forum_id = (int)array_shift($sys_parameters);
$action = post('action');

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
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
$template->set_var('forum_class', 'TD-menu-active');
$template->set_title('Admin :: forumi');

$template->set_var('forum_id', $forum_id, 'FILE_middle');
set_forum($template, $forum_id);

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
		set_themes($template, $items);
		# jauna teema
		$template->enable('BLOCK_forum_theme_new');
	} else {
		$template->enable('BLOCK_forum_resid');
		$template->set_file('FILE_comment_list', 'comment/list.tpl');
		$template->copy_block('BLOCK_forum_comments', 'FILE_comment_list');

		$RC = new ResComment();
		$comments = $RC->Get(array(
			'res_id'=>$forum_data['res_id'],
			'c_visible'=>Res::STATE_ALL,
			));

		include('module/admin/comment/list.inc.php');
	}

	$template->enable('BLOCK_forum_edit');

	$template->set_array($forum_data, 'BLOCK_forum_edit');
	$template->set_var('forum_data', parse_form_data($forum_data['forum_data']), 'BLOCK_forum_edit');

	if($forum_data['forum_active'] == Res::STATE_ACTIVE)
	{
		$template->set_var('forum_active_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_inactive_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_closed'] == Forum::CLOSED)
	{
		$template->set_var('forum_closed_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_open_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_allowchilds'] == Forum::ALLOW_CHILDS)
	{
		$template->set_var('forum_allowchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_prohibitchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	foreach($forum->types as $type_id=>$type_name){
		$template->set_var('type_id', $type_id, 'BLOCK_forum_type_list');
		$template->set_var('type_name', $type_name, 'BLOCK_forum_type_list');
		$template->set_var('type_id_selected', '', 'BLOCK_forum_type_list');
		if($type_id == $forum_data['type_id']){
			$template->set_var('type_id_selected', ' selected="selected"', 'BLOCK_forum_type_list');
		}
		$template->parse_block('BLOCK_forum_type_list', TMPL_APPEND);
	}

	$module->set_modules_all($template, $forum_data['forum_modid'], 'BLOCK_modules_under_list');
	$template->set_var("forum_display_$forum_data[forum_display]_selected", ' selected="selected"', 'BLOCK_forum_edit');
} else {
	# Root
	set_themes($template, $items);

	# jauna teema
	$template->enable('BLOCK_forum_theme_new');
}

$template->out();

