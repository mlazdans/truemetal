<?php declare(strict_types = 1);

// \{([^{]*)\}
// <?=$this->$1 ? >

(function(){
	global $sys_module_id, $sys_parameters;

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

	$template = new MainTemplate;

	if($art_id){
		$T = article($template, $art_id, $hl, $article_route);
	} elseif($sys_module_id == 'article' || empty($sys_module_id)){
		$T = mainpage($template, $page, $art_per_page);
	} else {
		$T = article_list($template, $page, $hl);
	}

	$template->MiddleBlock = $T;

	$template->set_right_defaults();
	$template->print();
})();

function article_list(MainTemplate $template, int $page, string $hl) //: ArticleListTemplate
{
	global $sys_module_id;

	if(!($module = ModulesEntity::get_by_module_id($sys_module_id, nil))){
		$template->not_found();
		$template->set_right_defaults();
		$template->print();
		return;
	}

	$art_title = '';
	$art_title = $module->module_name;

	$tc = 0;
	$tp = 0;

	if($page && ($page <= $tp))
		$art_title .= sprintf(" %d. lapa ", $page);

	$art_title .= ($hl ? sprintf(" - meklēšana: %s", $hl) : "");

	$template->set_title($art_title);
	// if(isset($_pointer['_data_']['module_id']))
	// 	$template->Index->set_var("menu_active_".$_pointer['_data_']['module_id'], "_over");
}
