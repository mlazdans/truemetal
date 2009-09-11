<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

if($comments)
{
	$template->enable('BLOCK_comments');
} else {
	$template->enable('BLOCK_nocomments');
}

foreach($comments as $item)
{
	$template->set_array($item, 'BLOCK_comment_item');
	if($item['c_visible'] == COMMENT_VISIBLE)
	{
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

