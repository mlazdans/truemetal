<?php declare(strict_types = 1);

$art_per_page = 10;

$hl = get("hl");

$art_id = array_shift($sys_parameters);
$page = 0;
if($art_id == "page")
{
	$page = (int)array_shift($sys_parameters);
}
$article_route = $art_id??"";
$art_id = (int)$art_id;

$template = new MainModule($sys_module_id);

# Get some root module ('mod_moid'=>false)
if(!($module = Module::load(['module_id'=>$sys_module_id, 'mod_modid'=>false])))
{
	$template->not_found();
	$template->set_right_defaults();
	$template->out(null);
}

$art_title = '';
$art_title = $module['module_name'];

$tc = 0;
$tp = 0;

if($art_id)
{
	$T = article($template, $art_id, $hl, $article_route);
} else {
	$T = article_list($template, $page, $art_per_page);
}

if($page && ($page <= $tp))
	$art_title .= sprintf(" %d. lapa ", $page);

$art_title .= ($hl ? sprintf(" - meklēšana: %s", $hl) : "");

$template->set_title($art_title);
if(isset($_pointer['_data_']['module_id']))
	$template->Index->set_var("menu_active_".$_pointer['_data_']['module_id'], "_over");

$template->set_right_defaults();
$template->out($T??null);
