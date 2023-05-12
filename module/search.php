<?php declare(strict_types = 1);
/******************************************************************************
|* doc_source_id
|*	1 - articles
|*	2 - reviews
|*	3 - forum
|*
|*  id_offset - lai dabūtu unikālus id unionā (check sphinx config)
|*
|*/

$search_msg = array();
$DOC_SOURCES = array(
	1=>array(
		//'id_offset'=>0,
		//'id_offset_titles'=>10000 + 10000,
		'name'=>"Ziņas",
		),
	2=>array(
		//'id_offset'=>0,
		//'id_offset_titles'=>10000 + 10000,
		'name'=>"Recenzijas",
		),
	3=>array(
		//'id_offset'=>10000,
		//'id_offset_titles'=>10000 + 10000 + 10000,
		'name'=>"Forums",
		),
	);

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	if(!User::logged()){
		$search_msg[] = "Meklētājs tikai reģistrētiem lietotājiem";
	}
	$search_log = post('spam') ? false : true;
	$search_q = post('search_q');
} else {
	$search_log = false;
	$search_q = urldecode(get('search_q'));
}

$ent_search_q = ent($search_q);

$template = new MainModule($sys_module_id, 'index.tpl');
$T = $template->add_file('search.tpl');
$template->set_title("Meklēšana: $ent_search_q");
$template->set_descr("Metāliskais meklētājs");

if($search_q && (mb_strlen($search_q) < 3))
	$search_msg[] = "Jāievada vismaz 3 simbolus";

$T->set_var("doc_count", 0);
$T->set_var('search_q', $ent_search_q);
$T->set_var('search_q_name', ": $ent_search_q");

if($search_q && !$search_msg)
{
	include('module/search/search.inc.php');
} else {
	$T->enable('BLOCK_search_help');
	$T->set_var('section_article_checked', ' checked="checked"');
	$T->set_var('section_reviews_checked', ' checked="checked"');
	$T->set_var('section_forum_checked', ' checked="checked"');
}

if($search_msg)
{
	$T->enable('BLOCK_search');
	$T->enable('BLOCK_search_msg');
	foreach($search_msg as $msg)
	{
		$T->set_var('search_msg', $msg, 'BLOCK_search_msg');
		$T->parse_block('BLOCK_search_msg_list', TMPL_APPEND);
	}
}

$template->set_right_defaults();
$template->out($T);

