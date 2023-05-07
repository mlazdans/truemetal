<?php

$hl = rawurldecode(get("hl"));

$action = post('action');

$fpp = 20;
$pages_visible_to_sides = 8;
$forum_id_urlized = rawurldecode(array_shift($sys_parameters)??"");
$forum_id = (int)$forum_id_urlized;

$page = array_shift($sys_parameters);

if($page == 'page'){
	$page_id = (int)array_shift($sys_parameters);
}

if(empty($page_id)){
	$page_id = 1;
}

if($forum_id)
{
	if(!($forum_data = (new Forum())->load(["forum_id"=>$forum_id])))
	{
		header("Location: $module_root/");
		return;
	}
} else {
	$forum_data = (new Forum())->load([
		"forum_forumid"=>0,
		"order"=>"forum_id ASC",
	]);
}

$template = new MainModule($sys_module_id);

# ja izveeleets forums, paraadam teemu sarakstu
$forum_title = 'Diskusijas';
$forum_descr = "Metāliskās diskusijas";

if($forum_id)
{
	# NOTE: redirektējam uz jaunajām adresēm, pēc gada (2011-04-30) varēs noņemt
	$forum_name_urlized = urlize($forum_data['forum_name']);
	$test_urlized = "$forum_id-$forum_name_urlized";
	if($forum_name_urlized && ($test_urlized != $forum_id_urlized))
	{
		$qs = (empty($_SERVER['QUERY_STRING']) ? "" : "?".$_SERVER['QUERY_STRING']);
		$new_url = "$module_root/$test_urlized$qs";
		header("Location: $new_url", true, 301);
		return;
	}
	#

	$forum_title .= ' - '.$forum_data['forum_name'].($hl ? sprintf(", meklēšana: %s", $hl) : "");
	$forum_descr .= ($hl ? sprintf(", meklēšana: %s", $hl) : "").' - '.$forum_data['forum_name'];
	if($page == 'page')
	{
		$forum_title .= " - $page_id. lapa";
		$forum_descr .= " - $page_id. lapa";
	}

	// $template->set_var('current_forum_id', $forum_id);
	// $template->set_var('current_forum_name_urlized', rawurlencode(urlize($forum_data['forum_name'])));

	# Subtēma TODO: jānotestē
	if($forum_data['forum_allowchilds'] == Forum::ALLOW_CHILDS)
	{
		// include('forum/theme.inc.php');
		$T = forum_themes($forum_id, $forum_data, $template, $action, $fpp, $page_id, $pages_visible_to_sides);
	} else {
		$T = forum_det($forum_id, $forum_data, $template, $action, $hl);
		// include('forum/det.inc.php');
	}
} else {
	$template->set_descr("Metāliskais forums");

	$T = $template->add_file('forum.tpl');

	if($forum_data)
	{
		$T->enable('BLOCK_forum');
		foreach($forum_data as $item)
		{
			$T->enable_if(Forum::hasNewThemes($item), 'BLOCK_comments_new');
			$T->set_array($item);
			$T->set_var('forum_name_urlized', rawurlencode(urlize($item['forum_name'])));
			$T->set_var('forum_date', proc_date($item['forum_entered']));
			$T->parse_block('BLOCK_forum', TMPL_APPEND);
		}
	} else {
		$T->enable('BLOCK_noforum');
	}
}

if(isset($T)){

	$template->Index->set_var("menu_active_forum", "_over");

	$template->set_title($forum_title);
	$template->set_descr($forum_descr);

	$template->set_events();
	$template->set_recent_forum();
	$template->set_online();
	$template->set_login();
	$template->set_search();
	$template->set_jubilars();

	$template->out($T);
}
