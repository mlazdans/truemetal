<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template = new MainModule($sys_template_root, 'whatsnew');
$template->set_title('Kas jauns');
$template->set_file('FILE_module', 'whatsnew.tpl');
$template->copy_block('BLOCK_middle', 'FILE_module');

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	$template->enable('BLOCK_not_loged');
} else {
	$template->enable('BLOCK_whatsnew');

	# Forum
	$Forum = new Forum;
	$data = $Forum->load(array(
		"order"=>'forum_lastcommentdate DESC',
		"limit"=>50,
		"forum_allowchilds"=>FORUM_PROHIBITCHILDS,
		));

	if($data)
	{
		$template->set_file('FILE_forum_recent', 'forum/recent.tpl');
		$template->copy_block('BLOCK_whatsnew_forum', 'FILE_forum_recent');
		foreach($data as $item)
		{
			$template->{(Forum::hasNewComments($item) ? "enable" : "disable")}('BLOCK_forum_r_comments_new');
			$template->set_var('forum_r_name', addslashes($item['forum_name']), 'BLOCK_forum_r_items');
			$template->set_var('forum_r_comment_count', $item['forum_comment_count'], 'BLOCK_forum_r_items');
			$template->set_var('forum_r_path', "forum/{$item['forum_id']}-".rawurlencode(urlize($item["forum_name"])), 'BLOCK_forum_r_items');
			$template->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
		}
	}

	# Articles
	$Article = new Article;
	$data = $Article->load(array(
		'order'=>'art_comment_lastdate DESC',
		'limit'=>50,
		));

	if($data)
	{
		$template->set_file('FILE_comment_recent', 'right/comment_recent.tpl');
		$template->copy_block('BLOCK_whatsnew_comments', 'FILE_comment_recent');
		foreach($data as $item)
		{
			$template->{(Article::hasNewComments($item) ? "enable" : "disable")}('BLOCK_comment_r_comments_new');

			$template->set_var('comment_r_name', $item['art_name'], 'BLOCK_comment_r_items');
			$template->set_var('comment_r_comment_count', $item['art_comment_count'], 'BLOCK_comment_r_items');
			$template->set_var('comment_r_path', "{$item['module_id']}/{$item['art_id']}-".urlize($item['art_name']), 'BLOCK_comment_r_items');
			$template->parse_block('BLOCK_comment_r_items', TMPL_APPEND);
		}
	}
}

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_search();

$template->out();

