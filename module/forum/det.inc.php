<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/ResComment.php');

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

if(($forum_data['forum_closed'] == FORUM_OPEN) && ($action == 'add_comment') && user_loged())
{
	//$table = 'forum';
	//$table_id = $forum_id;
	$res_id = $forum_data['res_id'];
	$data = post('data');
	$resDb = $db;
	if($c_id = include('module/comment/add.inc.php'))
	{
		$resDb->Commit();
		header("Location: $sys_http_root/forum/$forum_id-".rawurlencode(urlize($forum_data["forum_name"]))."#comment$c_id");
		return;
	}
}

$params = array(
	'res_id'=>$forum_data['res_id'],
	);
$params['order'] =
	isset($_SESSION['login']['l_forumsort_msg']) &&
	($_SESSION['login']['l_forumsort_msg'] == FORUM_SORT_DESC)
	? "c_entered DESC"
	: "c_entered";

$RC = new ResComment();
$comments = $RC->Get($params);

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

if($forum_data['forum_closed'] == FORUM_CLOSED)
{
	$template->disable('BLOCK_addcomment');
	$template->enable('BLOCK_forum_closed');
}

set_forum($template, $forum_id);

