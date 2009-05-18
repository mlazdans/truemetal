<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

function set_modules(&$template, &$data, $d = 0, $c = 0) {
	global $forum;

	if($d == 2)
		return 0;

	foreach($data as $item) {
		++$c;
		$template->set_var('forum_nr', $c, 'BLOCK_forum');
		$template->set_var('forum_id', $item['forum_id'], 'BLOCK_forum');
		$template->set_var('forum_name', $item['forum_name'], 'BLOCK_forum');

		// ja aktiivs vai nee - kaukaa iekraaso to
		$template->disable('BLOCK_forum_active');
		$template->disable('BLOCK_forum_inactive');
		if($item['forum_active'] == 'Y') {
			$template->enable('BLOCK_forum_active');
			$template->set_var('forum_color_class', 'box-normal', 'BLOCK_forum');
		} else {
			$template->enable('BLOCK_forum_inactive');
			$template->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum');
		}

		$template->set_var('forum_padding', str_repeat('&nbsp;', 3 * $d));
		$template->parse_block('BLOCK_forum', TMPL_APPEND);
		//$forum_data = $forum->load(0, $item['forum_id'], FORUM_ALL);
		//$c = set_modules(&$template, $forum_data, $d + 1, $c);
		//if($c1)
			//$c = $c1;
	}

	return $c;
}

require_once('../classes/class.AdminModule.php');
require_once('../classes/class.Forum.php');

$forum = new Forum;

$forum_id = (integer)array_shift($sys_parameters);
$action = isset($_POST['action']) ? $_POST['action'] : '';

if($forum_id)
	$template = new AdminModule($sys_template_root.'/admin', 'forum_edit');
else
	$template = new AdminModule($sys_template_root.'/admin', 'forum');

$template->set_var('forum_class', 'TD-menu-active');

$template->set_title('Admin :: forumi');

if(in_array($action, array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'move_multiple')))
{
	if($forum->process_action($_POST, $action, FORUM_VALIDATE))
		if($forum_id)
			header("Location: $module_root/$forum_id/");
		else
			header("Location: $module_root/");
	exit;
}

if($action == 'save_forum') {
	if(isset($_POST['data'])) {
		if($forum->save($_POST['data'], FORUM_VALIDATE))
			if($forum_id)
				header("Location: $module_root/$forum_id/");
			else
				header("Location: $module_root/");
	}
	exit;
}

if($action == 'add_forum') {
	if(isset($_POST['data'])) {
		$_POST['data']['forum_allowchilds'] = 'Y';
		$_POST['data']['forum_active'] = 'N';
		if($forum->add($forum_id, $_POST['data'], FORUM_VALIDATE))
			if($forum_id)
				header("Location: $module_root/$forum_id/");
			else
				header("Location: $module_root/");
	}
	exit;
}

// ja skataam foruma zinjas
if($forum_id)
{
	$template->set_file('FILE_forum', 'tmpl.forum_edit.php');
	$template->copy_block('BLOCK_middle', 'FILE_forum');

	$forum_data = $forum->load($forum_id, 0, FORUM_ALL);
	set_forum($template, $forum_id);

	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS) {
		$template->set_file('FILE_forumdets', 'tmpl.forum_theme.php');
	} else {
		$template->set_file('FILE_forumdets', 'tmpl.forum_det.php');
	}
	/*
	if($tree = $forum->get_all_tree())
	{
		$template->enable('BLOCK_forum_forumid');
		$forum->set_all_tree($template, $tree);
	}
	*/
	//$template->copy_block('BLOCK_forumdets', 'FILE_forumdets');

	$template->set_var('forum1_id', $forum_data['forum_id'], 'BLOCK_middle');
	$template->set_var('forum1_name', $forum_data['forum_name'] ? $forum_data['forum_name'] : '---', 'BLOCK_middle');
	$template->set_var('forum1_username', $forum_data['forum_username'], 'BLOCK_middle');
	$template->set_var('forum1_useremail', $forum_data['forum_useremail'], 'BLOCK_middle');
	$template->set_var('forum1_userip', $forum_data['forum_userip'], 'BLOCK_middle');
	$template->set_var('forum1_entered', $forum_data['forum_entered'], 'BLOCK_middle');
	$template->set_var('forum1_data', $forum_data['forum_data'], 'BLOCK_middle');

	if($forum_data['forum_active'] == FORUM_ACTIVE)
		$template->set_var('forum1_active', ' selected', 'BLOCK_middle');
	else
		$template->set_var('forum1_inactive', ' selected', 'BLOCK_middle');

	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS)
		$template->set_var('forum1_allowchilds', ' selected', 'BLOCK_middle');
	else
		$template->set_var('forum1_prohibitchilds', ' selected', 'BLOCK_middle');

	// apaksitemi
	$forum_data = $forum->load(0, $forum_id, FORUM_ALL);

	if(count($forum_data))
	{
		$template->enable('BLOCK_forum');
		$template->copy_block('BLOCK_forumdets', 'FILE_forumdets');
	}

	$c = 0;
	foreach($forum_data as $item)
	{
		++$c;
		$template->set_var('forum_nr', $c);
		//$item['forum_datacompiled'] = mb_substr($item['forum_datacompiled'], 0, 100).'...';

		$template->set_array($item, 'BLOCK_forum');

		// ja aktiivs vai nee - kaukaa iekraaso to
		$template->disable('BLOCK_forum_active');
		$template->disable('BLOCK_forum_inactive');
		if($item['forum_active'] == 'Y')
		{
			$template->enable('BLOCK_forum_active');
			$template->set_var('forum_color_class', 'box-active', 'BLOCK_forum');
		} else {
			$template->enable('BLOCK_forum_inactive');
			$template->set_var('forum_color_class', 'box-inactive', 'BLOCK_forum');
		}

		$template->parse_block('BLOCK_forum', TMPL_APPEND);
	}
	$template->set_var('item_count', $c);
	// ja forumu sarakstu
} else {
	$forum_data = $forum->load($forum_id, 0, FORUM_ALL);

	$template->set_var('forum1_id', 0);
	$template->set_file('FILE_forum', 'tmpl.forum.php');
	$template->copy_block('BLOCK_middle', 'FILE_forum');

	if(count($forum_data))
		$template->enable('BLOCK_forum');

	$c = set_modules($template, $forum_data);
	$template->set_var('item_count', $c);
} // forum_id

// jauna teema
$template->set_file('FILE_forumnew', 'tmpl.forum_new.php');
$template->copy_block('BLOCK_forumnew', 'FILE_forumnew');

$template->out();

?>
