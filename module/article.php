<?php declare(strict_types = 1);

// \{([^{]*)\}
// <?=$this->$1 ? >

(function(){
	global $sys_parameters;

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
	} else {
		$T = mainpage($page, $art_per_page);
	}

	$template->MiddleBlock = $T;

	$template->set_right_defaults();
	$template->print();
})();
