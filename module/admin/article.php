<?php declare(strict_types = 1);

$action = post('action');
$art_id = array_shift($sys_parameters);

$article = new Article();
$module = new Module();

$template = new AdminModule($admin_module);
$template->set_title('Admin :: raksti');

$T = $template->add_file("admin/article.tpl");

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
		$T->enable('BLOCK_article_error');
		$T->set_var('error_msg', $article->error_msg, 'BLOCK_article_error');
	}
	$template->out($T);
	return;
}

# new
if($action == 'art_new')
{
	$module->set_modules_all($T, 0, 'BLOCK_modules_under_list');
	$T->enable('BLOCK_article_edit');
	$T->set_var('art_name_edit', 'jauns', 'BLOCK_article_edit');
	$template->out($T);
	return;
}

# List
if(!$art_id)
{
	$T->enable('BLOCK_articles_list');

	$articles = $article->load(array(
		'art_active'=>Res::STATE_ALL,
		'order'=>'m.mod_id, a.art_entered DESC',
		));

	if(count($articles))
		$T->enable('BLOCK_articles');
	else
		$T->enable('BLOCK_noarticles');

	$article_count = 0;
	$BLOCK_article_item = $T->get_block('BLOCK_article_item');
	foreach($articles as $item)
	{
		++$article_count;
		$BLOCK_article_item->set_var('article_nr', $article_count);
		$BLOCK_article_item->set_var('art_name', $item['art_name']);
		$BLOCK_article_item->set_var('art_id', $item['art_id']);
		$BLOCK_article_item->set_var('module_id', $item['module_id']);
		$BLOCK_article_item->set_var('art_entered', $item['art_entered']);

		$BLOCK_article_item->set_var('art_color_class', 'box-normal');
		if($item['art_active'] != Res::STATE_ACTIVE){
			$BLOCK_article_item->set_var('art_color_class', 'box-inactive');
		}

		$BLOCK_article_item->parse(TMPL_APPEND);
	}

	$T->set_var('article_count', $article_count);
} elseif($art = $article->load(array(
	'art_id'=>$art_id,
	'art_active'=>Res::STATE_ALL,
	)))
{
	# Edit
	$T->enable('BLOCK_article_edit');
	$T->set_var('art_name_edit', $art['art_name'], 'BLOCK_article_edit');

	if($art['art_active'] == Res::STATE_ACTIVE)
		$T->set_var('art_active_y', ' selected="selected"');
	else
		$T->set_var('art_active_n', ' selected="selected"');

	$art['art_intro'] = specialchars($art['art_intro']);
	$art['art_data'] = specialchars($art['art_data']);
	$T->set_array($art);

	$module->set_modules_all($T, $art['art_modid'], 'BLOCK_modules_under_list');

	$RC = new ResComment();
	$comments = $RC->Get(array(
		'res_id'=>$art['res_id'],
		'c_visible'=>Res::STATE_ALL,
		));

	$C = new_template("admin/comment/list.tpl");
	admin_comment_list($C, $comments);

	$T->set_block_string('BLOCK_article_comments', $C->parse());

}

$template->out($T);

