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

# TODO: Vajag uztaisīt:
# 1) lai rāda foruma datus
# 2) uztaisīt balsošanu par articles un forum
# 3) pārkopēt foruma pirmā komenta votes uz foruma votēm
# 4) izvākt pirmo foruma komentu
/*
if($forum_data)
{
	$template->enable('BLOCK_forum');
	$template->set_array($forum_data, 'BLOCK_forum');
}
*/
#

if(user_loged())
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

$params['sort'] =
	isset($_SESSION['login']['l_forumsort_msg']) &&
	($_SESSION['login']['l_forumsort_msg'] == FORUM_SORT_DESC)
	? "c_entered DESC"
	: "c_entered";

$comments = $CC->get($params);

# XXX : hack, vajag rādīt pa taisno foruma ierakstu
if(($forum_data['forum_display'] == Forum::DISPLAY_DATA) && !empty($comments[0]))
{
	# Ja sakārtots dilstoši, tad jāaiztiek ir pēdējais komments
	if(
		isset($_SESSION['login']['l_forumsort_msg']) &&
		($_SESSION['login']['l_forumsort_msg'] == FORUM_SORT_DESC)
		)
	{
		array_unshift($comments, array_pop($comments));
		//$comments = array_merge(array(array_pop($comments)), $comments);
	}
	$comments[0]['c_datacompiled'] = $forum_data['forum_data'];
}

include("module/comment/list.inc.php");

set_forum($template, $forum_id);

