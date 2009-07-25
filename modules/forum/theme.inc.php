<?php

$_SESSION['forums']['viewed'][$forum_id] = $forum_data['forum_themecount'];

if($action == 'add_theme')
{
	$error = false;
	$data = post('data');
	$forum->validate($data);
	$data['forum_useremail'] = $_SESSION['login']['l_email'];
	$data['forum_username'] = $_SESSION['login']['l_nick'];
	$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;

	if(!$data['forum_name'])
	{
		$error = true;
		$template->enable('BLOCK_forumname_error');
	}

	if(!$data['forum_data'])
	{
		$error = true;
		$template->enable('BLOCK_forumdata_error');
	}

	if(!$error)
	{
		if($id = $forum->add($forum_id, $data))
		{
			header("Location: $module_root/$id/");
			return;
		}
	}
	parse_form_data($data);
	$template->set_array($data, 'FILE_forum');
} // add_theme


$template->set_file('FILE_forum', 'tmpl.forum_theme.php');
$template->copy_block('BLOCK_middle', 'FILE_forum');

if(user_loged())
{
	$template->enable('BLOCK_loggedin');
	$template->set_var('forumd_username', $_SESSION['login']['l_nick'], 'FILE_forum');
} else {
	$template->enable('BLOCK_notloggedin');
}

$forum_items = new Forum;
$forum_items->setItemsPerPage($fpp);
$forum_items->setPage($page_id);
if(
	isset($_SESSION['login']['l_forumsort_themes']) &&
	($_SESSION['login']['l_forumsort_themes'] == FORUM_SORT_LAST_COMMENT)
)
{
	//$items = $forum_items->load(0, $forum_id, FORUM_ACTIVE, "forum_lastcommentdate DESC");
	$items = $forum_items->load(array(
		"forum_forumid"=>$forum_id,
		"order"=>"forum_lastcommentdate DESC",
		));
	$template->enable('BLOCK_info_sort_C');
} else {
	//$items = $forum_items->load(0, $forum_id);
	$items = $forum_items->load(array(
		"forum_forumid"=>$forum_id,
		));
	$template->enable('BLOCK_info_sort_T');
}

if($items)
{
	$template->enable('BLOCK_forum');
} else {
	$template->enable('BLOCK_noforum');
}

foreach($items as $item)
{
	$old_forum_childcount =
	isset($_SESSION['forums']['viewed'][$item['forum_id']]) ?
	$_SESSION['forums']['viewed'][$item['forum_id']] :
	0;

	if($item['forum_comment_count'] > $old_forum_childcount)
		$template->enable('BLOCK_comments_new');
	else
		$template->disable('BLOCK_comments_new');

	$template->set_array($item, 'BLOCK_forum');
	$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
	$template->parse_block('BLOCK_forum', TMPL_APPEND);
}

$forum_count = $forum_items->getThemeCount($forum_id);
include('pages.inc.php');

set_forum($template, $forum_id);

