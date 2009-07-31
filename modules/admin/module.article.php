<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$action = isset($_POST['action']) ? $_POST['action'] : '';

$art_id = array_shift($sys_parameters);

require_once('../classes/class.Article.php');
require_once('../classes/class.Module.php');

$art_modid = isset($_POST['art_modid']) ? (integer)$_POST['art_modid'] : '';

$article = new Article();

$module = new Module();

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: raksti');

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'show_multiple', 'hide_multiple');

if(in_array($action, $actions)) {
	if($article->process_action($_POST, $action))
		if($art_id)
			header("Location: $module_root/$art_id/");
		else
			header("Location: $module_root/");
	return;
}

/*
$comment_actions = array('comment_delete_multiple', 'comment_show_multiple', 'comment_hide_multiple');

if(in_array($action, $comment_actions)) {
	if($art_id) {
		if($article->comment_process_action($_POST, $action))
			header("Location: $module_root/$art_id/");
	} else
		header("Location: $module_root/");
	exit;
}
*/

# save
if($action == 'art_save') {
	if($id = $article->save($art_id, $_POST['data'])) {
		header("Location: $module_root/$id/");
		return;
	} else {
		$template->enable('BLOCK_article_error');
		$template->set_var('error_msg', $msg, 'BLOCK_article_error');
	}
	$template->out();
	return;
}

# new
if($action == 'art_new') {
	$template->init_editor();
	$template->enable('BLOCK_article_edit');
	$template->set_var('art_name_edit', 'jauns', 'BLOCK_article_edit');
	$template->out();
	return;
	/*
	if($art_modid) {
		$template->enable('BLOCK_article_edit');
		//$template->set_var('module_name', $module->data[$art_modid]['module_name']);

		$template->set_var('art_modid', $art_modid);
		$template->init_editor();
	} else {
		$template->enable('BLOCK_modules_under');
		//$module->set_modules_all($template, 0, 'BLOCK_modules_under_list');
	}
	*/
}

# List
if(!$art_id)
{
	$template->enable('BLOCK_articles_list');

	//$article->set_order('m.mod_id, a.art_entered DESC');
	//$articles = $article->load(0, 0, ARTICLE_ALL, ARTICLE_ALL);
	$articles = $article->load(array(
		'art_active'=>ARTICLE_ALL,
		'order'=>'m.mod_id, a.art_entered DESC',
		));

	if(count($articles))
		$template->enable('BLOCK_articles');
	else
		$template->enable('BLOCK_noarticles');

	$article_count = 0;
	foreach($articles as $item)
	{
		++$article_count;
		$template->set_var('article_nr', $article_count, 'BLOCK_article_item');
		$template->set_var('art_name', $item['art_name'], 'BLOCK_article_item');
		$template->set_var('art_id', $item['art_id'], 'BLOCK_article_item');
		$template->set_var('module_id', $item['module_id'], 'BLOCK_article_item');

		$template->set_var('art_color_class', 'box-normal', 'BLOCK_article_item');
		if($item['art_active'] != ARTICLE_ACTIVE)
			$template->set_var('art_color_class', 'box-inactive', 'BLOCK_article_item');

		$template->parse_block('BLOCK_article_item', TMPL_APPEND);
	} // foreach articles
	$template->set_var('article_count', $article_count);
# Edit
} elseif($art = $article->load(array(
	'art_id'=>$art_id,
	'art_active'=>ARTICLE_ALL,
	)))
{
	$template->enable('BLOCK_article_edit');
	$template->set_var('art_name_edit', $art['art_name'], 'BLOCK_article_edit');

	// editor
	$template->init_editor();

	//$template->set_var('art_comments', $art['art_comments'] == 'Y' ? ' checked' : '');

	if($art['art_type'] == ARTICLE_TYPE_OPEN)
		$template->set_var('art_type_o', ' selected="selected"');
	elseif($art['art_type'] == ARTICLE_TYPE_REGISTRATED)
		$template->set_var('art_type_r', ' selected="selected"');

	if($art['art_comments'] == ARTICLE_COMMENTS)
		$template->set_var('art_comments_y', ' selected="selected"');
	else
		$template->set_var('art_comments_n', ' selected="selected"');

	if($art['art_active'] == ARTICLE_ACTIVE)
		$template->set_var('art_active_y', ' selected="selected"');
	else
		$template->set_var('art_active_n', ' selected="selected"');

	parse_form_data_array($art);
	$template->set_array($art);

	// komentaari
	/*
	$comments = $article->load_comments($art_id, COMMENT_ALL);
	if(count($comments))
		$template->enable('BLOCK_article_comments');

	$comment_count = 0;
	foreach($comments as $comment)
	{
		++$comment_count;
		$template->set_array($comment);

		if($comment['ac_visible'] == COMMENT_VISIBLE)
		{
			$template->enable('BLOCK_comment_active');
			$template->disable('BLOCK_comment_inactive');
			$template->set_var('comment_color_class', 'box-normal');
		} else {
			$template->disable('BLOCK_comment_active');
			$template->enable('BLOCK_comment_inactive');
			$template->set_var('comment_color_class', 'box-invisible');
		}

		$template->set_var('comment_nr', $comment_count);
		$template->parse_block('BLOCK_comment', TMPL_APPEND);
	} // foreach comments
	$template->set_var('comment_count', $comment_count);
	*/
}

$template->out();

