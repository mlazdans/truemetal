<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Article.php');
require_once('lib/Module.php');
require_once('lib/Comment.php');
require_once('lib/ResComment.php');

$action = post('action');
$art_id = array_shift($sys_parameters);

$article = new Article();
$module = new Module();

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: raksti');

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include('module/admin/comment/action.inc.php'))
	{
		header("Location: ".($art_id ? "$module_root/$art_id/" : "$module_root"));
	}
	return;
}

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'show_multiple', 'hide_multiple');
if(in_array($action, $actions))
{
	if($article->process_action($_POST, $action))
		if($art_id)
			header("Location: $module_root/$art_id/");
		else
			header("Location: $module_root/");
	return;
}

# save
if($action == 'art_save')
{
	if($id = $article->save($art_id, $_POST['data'])) {
		header("Location: $module_root/$id/");
		return;
	} else {
		$template->enable('BLOCK_article_error');
		$template->set_var('error_msg', $article->error_msg, 'BLOCK_article_error');
	}
	$template->out();
	return;
}

# new
if($action == 'art_new')
{
	$module->set_modules_all($template, 0, 'BLOCK_modules_under_list');
	$template->enable('BLOCK_article_edit');
	$template->set_var('art_name_edit', 'jauns', 'BLOCK_article_edit');
	$template->out();
	return;
}

# List
if(!$art_id)
{
	$template->enable('BLOCK_articles_list');

	$articles = $article->load(array(
		'art_active'=>Res::STATE_ALL,
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
		if($item['art_active'] != Res::STATE_ACTIVE)
			$template->set_var('art_color_class', 'box-inactive', 'BLOCK_article_item');

		$template->parse_block('BLOCK_article_item', TMPL_APPEND);
	}
	$template->set_var('article_count', $article_count);
} elseif($art = $article->load(array(
	'art_id'=>$art_id,
	'art_active'=>Res::STATE_ALL,
	)))
{
	# Edit
	$template->enable('BLOCK_article_edit');
	$template->set_var('art_name_edit', $art['art_name'], 'BLOCK_article_edit');

	if($art['art_active'] == Res::STATE_ACTIVE)
		$template->set_var('art_active_y', ' selected="selected"');
	else
		$template->set_var('art_active_n', ' selected="selected"');

	$art['art_intro'] = parse_form_data($art['art_intro']);
	$art['art_data'] = parse_form_data($art['art_data']);
	$template->set_array($art);

	$module->set_modules_all($template, $art['art_modid'], 'BLOCK_modules_under_list');

	# komentaari
	$template->set_file('FILE_comment_list', 'comment/list.tpl');
	$template->copy_block('BLOCK_article_comments', 'FILE_comment_list');

	$RC = new ResComment();
	$comments = $RC->Get(array(
		'res_id'=>$art['res_id'],
		'c_visible'=>Res::STATE_ALL,
		));

	include('module/admin/comment/list.inc.php');
}

$template->out();

