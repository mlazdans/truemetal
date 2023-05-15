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

$template = new MainModule($sys_module_id);

# ja izveeleets forums, paraadam teemu sarakstu
$forum_title = 'Diskusijas';
$forum_descr = "Metāliskās diskusijas";

if($forum_id)
{
	if(!($forum_data = (new Forum())->load(["forum_id"=>$forum_id])))
	{
		header("Location: /forum/");
		return;
	}

	$res_name_urlized = urlize($forum_data['res_name']);
	$test_urlized = "$forum_id-$res_name_urlized";
	if($res_name_urlized && ($test_urlized != $forum_id_urlized))
	{
		$qs = (empty($_SERVER['QUERY_STRING']) ? "" : "?".$_SERVER['QUERY_STRING']);
		$new_url = "/forum/$test_urlized$qs";
		header("Location: $new_url", true, 301);
		return;
	}

	$forum_title .= ' - '.$forum_data['res_name'].($hl ? sprintf(", meklēšana: %s", $hl) : "");
	$forum_descr .= ($hl ? sprintf(", meklēšana: %s", $hl) : "").' - '.$forum_data['res_name'];
	if($page == 'page')
	{
		$forum_title .= " - $page_id. lapa";
		$forum_descr .= " - $page_id. lapa";
	}

	if($forum_data['forum_allow_childs'])
	{
		$T = forum_themes($forum_id, $forum_data, $template, $action, $fpp, $page_id, $pages_visible_to_sides);
	} else {
		$T = forum_det($forum_id, $forum_data, $template, $action, $hl);
	}
} else {
	$T = forum_root($template);
}

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
