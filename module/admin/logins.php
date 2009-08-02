<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$action = isset($_POST['action']) ? $_POST['action'] : '';

$l_id = array_shift($sys_parameters);

require_once('lib/Logins.php');
require_once('lib/Module.php');
require_once('lib/Article.php');
require_once('lib/Forum.php');

$logins = new Logins;
$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: logini');

/* ------------------------------------------------------------------------- */

function logins_error($msg, &$template) {
	$template->enable('BLOCK_logins_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple');

if(in_array($action, $actions)) {
	if($logins->process_action($_POST, $action))
		if(!empty($p_id))
			header("Location: $module_root/$p_id/");
		else
			header("Location: $module_root/");
	exit;
}

// saraksts
if(!$l_id) {
	$l = $logins->load('', '', LOGIN_ALL, LOGIN_ALL);

	if(count($l))
		$template->enable('BLOCK_logins_list');
	else
		$template->enable('BLOCK_nologins');

	$logins_count = 0;
	foreach($l as $item) {
		++$logins_count;
		$template->set_var('logins_nr', $logins_count);
		$template->set_array($item);

		$template->set_var('l_color_class', 'box-normal');
		if($item['l_active'] != LOGIN_ACTIVE)
			$template->set_var('l_color_class', 'box-inactive');

		$template->parse_block('BLOCK_logins', TMPL_APPEND);
	} // foreach logins
	$template->set_var('logins_count', $logins_count);
} else {
	// view & edit
	$l = $logins->load_by_id($l_id);
	$template->set_array($l);
	$template->enable('BLOCK_login_view');

	// ielaadeejam
	$article = new Article;
	$forum = new Forum;

	$art_comments = $article->load_by_userid($l_id);
	$forum_comments = $forum->load_by_userid($l_id);
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
}

$template->out();

?>