<?php

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
	$template->set_array($item, 'BLOCK_forum');
	$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
	$template->parse_block('BLOCK_forum', TMPL_APPEND);
}


