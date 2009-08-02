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
		if($item['forum_active'] == FORUM_ACTIVE) {
			$template->enable('BLOCK_forum_active');
			$template->set_var('forum_color_class', 'box-normal', 'BLOCK_forum_theme_item');
		} else {
			$template->enable('BLOCK_forum_inactive');
			$template->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum_theme_item');
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
require_once('lib/Comment.php');
require_once('lib/CommentConnect.php');

$forum = new Forum;

$forum_id = (int)array_shift($sys_parameters);
$action = post('action');

if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	$c_ids = post('c_id');
	if(!is_array($c_ids))
		$c_ids = array($c_id);

	$ok = true;
	$func = substr($action, 8);

	$db->AutoCommit(false);

	$Comment = new Comment;
	$Comment->setDb($db);
	foreach($c_ids as $c_id)
		$ok = $Comment->{$func}($c_id) ? $ok : false;

	if($ok)
	{
		$db->Commit();
		if($forum_id)
			header("Location: $module_root/$forum_id/");
		else
			header("Location: $module_root/");
	}

	return;
}

if(in_array($action, array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'move_multiple')))
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

	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS) {
		set_themes($template, $items);
		// jauna teema
		$template->enable('BLOCK_forum_theme_new');
	} else {

		$CC = new CommentConnect('forum');
		$CC->setDb($db);
		$comments = $CC->get(array(
			'cc_table_id'=>$forum_id,
			'c_visible'=>COMMENT_ALL,
			));

		if($comments) {
			$template->enable('BLOCK_comments');
		} else {
			$template->enable('BLOCK_nocomments');
		}

		foreach($comments as $item)
		{
			$template->set_array($item, 'BLOCK_comment_item');
			if($item['c_visible'] == COMMENT_VISIBLE) {
				$template->enable('BLOCK_c_visible');
				$template->disable('BLOCK_c_invisible');
				$template->set_var('c_color_class', 'box-normal', 'BLOCK_comment_item');
			} else {
				$template->enable('BLOCK_c_invisible');
				$template->disable('BLOCK_c_visible');
				$template->set_var('c_color_class', 'box-invisible', 'BLOCK_comment_item');
			}
			$template->parse_block('BLOCK_comment_item', TMPL_APPEND);
		}
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
	if($forum_data['forum_active'] == FORUM_ACTIVE)
	{
		$template->set_var('forum_active_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_inactive_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}

	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS)
	{
		$template->set_var('forum_allowchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	} else {
		$template->set_var('forum_prohibitchilds_sel', ' selected="selected"', 'BLOCK_forum_edit');
	}
} else {
	# Root
	set_themes($template, $items);
	// jauna teema
	$template->enable('BLOCK_forum_theme_new');
} // forum_id

$template->out();

