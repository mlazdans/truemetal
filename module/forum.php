<?php

$template = new MainModule($sys_module_id);
$forum_descr = $forum_title = 'Forums';

$hl = get("hl");
$action = post('action');
$forum_route = array_shift($sys_parameters);
$page = array_shift($sys_parameters);

if($page == 'page'){
	$page_id = (int)array_shift($sys_parameters);
}

if(empty($page_id)){
	$page_id = 1;
}


if($forum_route)
{
	$forum_data = (function(MainModule $template, string $forum_route): ?ViewResForumType
	{
		$forum_id = (int)$forum_route;

		if(!($forum_data = ViewResForumEntity::getById($forum_id)))
		{
			$template->not_found();
			return null;
		}

		# TODO: append query_string? ?hl=
		if($forum_route && !str_ends_with($forum_data->res_route, "/$forum_route"))
		{
			header("Location: $forum_data->res_route", true, 301);
			return null;
		}

		return $forum_data;
	})($template, $forum_route);

	if($forum_data)
	{
		$forum_title .= ' - '.$forum_data->res_name.($hl ? sprintf(", meklēšana: %s", $hl) : "");
		$forum_descr .= ($hl ? sprintf(", meklēšana: %s", $hl) : "").' - '.$forum_data->res_name;
		if($page == 'page')
		{
			$forum_title .= " - $page_id. lapa";
			$forum_descr .= " - $page_id. lapa";
		}

		if($forum_data->forum_allow_childs)
		{
			$fpp = 20;
			$pages_visible_to_sides = 8;

			$T = forum_themes($template, $forum_data, $action, $fpp, $page_id, $pages_visible_to_sides);
		} else {
			$T = forum_det($template, $forum_data, $action, $hl);
		}
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

$template->out($T??null);
