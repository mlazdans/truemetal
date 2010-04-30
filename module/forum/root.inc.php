<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

$template->set_file('FILE_forum', 'forum.tpl');
$template->copy_block('BLOCK_middle', 'FILE_forum');

if($forum_data)
{
	$template->enable('BLOCK_forum');
} else {
	$template->enable('BLOCK_noforum');
}

foreach($forum_data as $item)
{
	$template->{(Forum::hasNewThemes($item) ? "enable" : "disable")}('BLOCK_comments_new');

	$item['forum_name_urlized'] = rawurlencode(urlize($item['forum_name']));

	$template->set_array($item, 'BLOCK_forum');
	$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
	$template->parse_block('BLOCK_forum', TMPL_APPEND);
}

$template->set_descr("Metāliskais forums: mūzika, koncerti, tirgus, un pārsvarā ne par tēmu");

