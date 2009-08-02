<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

$template->set_file('FILE_forum', 'tmpl.forum.php');
$template->copy_block('BLOCK_middle', 'FILE_forum');

if($forum_data)
{
	$template->enable('BLOCK_forum');
} else {
	$template->enable('BLOCK_noforum');
}

foreach($forum_data as $item)
{
	$old_forum_childcount =
	isset($_SESSION['forums']['viewed'][$item['forum_id']]) ?
	$_SESSION['forums']['viewed'][$item['forum_id']] :
	0;

	if($item['forum_themecount'] > $old_forum_childcount)
		$template->enable('BLOCK_comments_new');
	else
		$template->disable('BLOCK_comments_new');

	$template->set_array($item, 'BLOCK_forum');
	$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
	$template->parse_block('BLOCK_forum', TMPL_APPEND);
}


