<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

$template->set_file('FILE_forum', 'forum/det.tpl');
$template->copy_block('BLOCK_middle', 'FILE_forum');

$template->set_file('FILE_forum_comments', 'comments.tpl');
$template->copy_block('BLOCK_forum_comments', 'FILE_forum_comments');

$_SESSION['forums']['viewed'][$forum_id] = $forum_data['forum_comment_count'];

if(($action == 'add_comment') && user_loged())
{
	$table = 'forum';
	$table_id = $forum_id;
	$data = post('data');
	if($c_id = include('module/comment/add.inc.php'))
	{
		$db->Commit();
		header("Location: $sys_http_root/forum/$forum_id/#comment$c_id");
		return;
	}
}

require_once('lib/CommentConnect.php');

$CC = new CommentConnect('forum');
$CC->setDb($db);

$params = array(
	'cc_table_id' => $forum_id,
	);
if(
	isset($_SESSION['login']['l_forumsort_msg']) &&
	($_SESSION['login']['l_forumsort_msg'] == FORUM_SORT_DESC)
)
{
	$params['sort'] = "c_entered DESC";
} else {
	$params['sort'] = "c_entered";
}

$comments = $CC->get($params);
include("module/comment/list.inc.php");

set_forum($template, $forum_id);
return;


//$forum_count = count($items);
//set_forum_items($template, $item_data);

if($items)
{
	$template->enable('BLOCK_forum');
} else {
	$template->enable('BLOCK_noforum');
}

foreach($items as $item)
{
	$template->set_array($item, 'BLOCK_forum');
	$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
	$template->parse_block('BLOCK_forum', TMPL_APPEND);
}

//$forum_count = $forum_items->getThemeCount($forum_id);
//include('pages.inc.php');

set_forum($template, $forum_id);

