<?php

$Logins = new Logins();
$login = $Logins->load(array(
	'l_id'=>$l_id,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL,
	));

$template->enable('BLOCK_login_edit');
$template->set_array($login, 'BLOCK_login_edit');

$YN = array(
	'l_active',
	'l_accepted',
	'l_emailvisible',
	'l_logedin',
	);

foreach($YN as $k)
{
	$v = sprintf("%s_%s_sel", $k, $login[$k]);
	$template->set_var($v, ' selected="selected"', 'BLOCK_login_edit');
}

/*
// ielaadeejam
$article = new Article;
$forum = new Forum;

//$art_comments = $article->load_by_userid($l_id);
//$forum_comments = $forum->load_by_userid($l_id);
$art_comments = false;
$forum_comments = false;
if($art_comments || $forum_comments)
{
	$template->enable('BLOCK_login_view_comments');
}

// zinjas
if($art_comments)
{
	$template->enable('BLOCK_login_view_article_c');
	$old_artid = -1;
	foreach($art_comments as $item)
	{
		$template->set_array($item);
		if($old_artid != $item['art_id'])
		{
			$template->enable('BLOCK_login_article');
			$template->parse_block('BLOCK_login_article', TMPL_APPEND);
		} else {
			$template->disable('BLOCK_login_article');
		}

		$template->set_var('comment_color_class', 'box-normal');
		if($item['art_active'] != ARTICLE_ACTIVE)
			$template->set_var('art_color_class', 'box-inactive');

		$template->reset_block('BLOCK_login_article');
		$template->parse_block('BLOCK_article_c', TMPL_APPEND);
		$old_artid = $item['art_id'];
	}
}

// foorums
if($forum_comments)
{
	$template->enable('BLOCK_login_view_forum_c');
	$old_forumid = -1;
	foreach($forum_comments as $item)
	{
		$template->set_array($item);
		if($old_forumid != $item['forum_id'])
		{
			$template->enable('BLOCK_login_forum');
			$template->parse_block('BLOCK_login_forum', TMPL_APPEND);
		} else {
			$template->disable('BLOCK_login_forum');
		}

		$template->reset_block('BLOCK_login_forum');
		$template->parse_block('BLOCK_forum_c', TMPL_APPEND);
		$old_forumid = $item['forum_id'];
	}
}
*/

