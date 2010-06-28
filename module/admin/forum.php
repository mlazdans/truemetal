<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

function set_themes(&$template, &$data, $d = 0, $c = 0) {
	global $forum;

	if($d == 2)
		return 0;

	if(count($data))
		$template->enable('BLOCK_forum_themes');
	else
		$template->enable('BLOCK_forum_nothemes');

	foreach($data as $item) {
		++$c;
		$template->set_var('forum_nr', $c, 'BLOCK_forum_theme_item');
		$template->set_var('forum_id', $item['forum_id'], 'BLOCK_forum_theme_item');
		$template->set_var('forum_name', $item['forum_name'], 'BLOCK_forum_theme_item');

		// ja aktiivs vai nee - kaukaa iekraaso to
		$template->disable('BLOCK_forum_active');
		$template->disable('BLOCK_forum_inactive');
		$template->disable('BLOCK_forum_closed');
		$template->disable('BLOCK_forum_open');
		if($item['forum_active'] == FORUM_ACTIVE) {
			$template->enable('BLOCK_forum_active');
			$template->set_var('forum_color_class', 'box-normal', 'BLOCK_forum_theme_item');
		} else {
			$template->enable('BLOCK_forum_inactive');
			$template->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum_theme_item');
		}

		if($item['forum_closed'] == FORUM_CLOSED) {
			$template->enable('BLOCK_forum_closed');
		} else {
			$template->enable('BLOCK_forum_open');
		}

		$template->set_var('forum_padding', str_repeat('&nbsp;', 3 * $d));
		$template->parse_block('BLOCK_forum_theme_item', TMPL_APPEND);
		//$forum_data = $forum->load(0, $item['forum_id'], FORUM_ALL);
		//$c = set_modules(&$template, $forum_data, $d + 1, $c);
		//if($c1)
			//$c = $c1;
	}
	$template->set_var('item_count', $c);

	return $c;
}

require_once('lib/AdminModule.php');
require_once('lib/Forum.php');
require_once('lib/Module.php');
require_once('lib/Comment.php');
require_once('lib/CommentConnect.php');

$forum = new Forum();
$module = new Module();

$forum_id = (int)array_shift($sys_parameters);
$action = post('action');

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include("module/admin/comment/action.inc.php"))
	{
		header("Location: ".($forum_id ? "$module_root/$forum_id/" : "$module_root"));
	}
	return;
}

if(in_array($action, array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'move_multiple', 'close_multiple', 'open_multiple')))
{
	if($forum->process_action($_POST, $action, FORUM_VALIDATE))
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
		if($forum->save($_POST['data'], FORUM_VALIDATE))
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
		if($forum->add($forum_id, $_POST['data'], FORUM_VALIDATE, FORUM_ALL))
		{
			if($forum_id)
				header("Location: $module_root/$forum_id/");
			else
				header("Location: $module_root/");
		}
	}
	return;
}

$template = new AdminModule($sys_template_root.'/admin', 'forum');
$template->set_var('forum_class', 'TD-menu-active');
$template->set_title('Admin :: forumi');

$template->set_var('forum_id', $forum_id, 'FILE_middle');
set_forum($template, $forum_id);

$items = $forum->load(array(
	'forum_forumid'=>$forum_id,
	'forum_active'=>FORUM_ALL,
	));

# Forums atvērts
if($forum_id)
{
	$forum_data = $forum->load(array(
		'forum_id'=>$forum_id,
		'forum_active'=>FORUM_ALL,
		));

	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS)
	{
		set_themes($template, $items);
		// jauna teema
		$template->enable('BLOCK_forum_theme_new');
	} else {
		$template->set_file('FILE_comment_list', 'comment/list.tpl');
		$template->copy_block('BLOCK_forum_comments', 'FILE_comment_list');

		$CC = new CommentConnect('forum');
		$CC->setDb($db);
		$comments = $CC->get(array(
			'cc_table_id'=>$forum_id,
			'c_visible'=>Comment::ALL,
			));

		include("module/admin/comment/list.inc.php");
	}

	/*
	if($tree = $forum->get_all_tree())
	{
		$template->enable('BLOCK_forum_forumid');
		$forum->set_all_tree($template, $tree);
	}
	*/
	//$template->copy_block('BLOCK_forumdets', 'FILE_forumdets');
	$template->enable('BLOCK_forum_edit');

	$template->set_array($forum_data, 'BLOCK_forum_edit');
	$template->set_var('forum_data', parse_form_data($forum_data['forum_data']), 'BLOCK_forum_edit');

	if($forum_data['forum_active'] == FORUM_ACTIVE)
	{
		$template->set_var('forum_active_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_inactive_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_closed'] == FORUM_CLOSED)
	{
		$template->set_var('forum_closed_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_open_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS)
	{
		$template->set_var('forum_allowchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_prohibitchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	$module->set_modules_all($template, $forum_data['forum_modid'], 'BLOCK_modules_under_list');
	$template->set_var("forum_display_$forum_data[forum_display]_selected", ' selected="selected"', 'BLOCK_forum_edit');
	/*
	if($forum_data['forum_showmainpage'])
		$template->set_var("forum_showmainpage_checked", ' checked="checked"', 'BLOCK_forum_edit');
	*/
} else {
	# Root
	set_themes($template, $items);
	// jauna teema
	$template->enable('BLOCK_forum_theme_new');
} // forum_id

$template->out();

