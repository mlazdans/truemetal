<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv


/******************************************************************************
|* doc_source_id
|*	1 - articles
|*	2 - reviews
|*	3 - forum
|*
|*  id_offset - lai dabūtu unikālus id unionā (check sphinx config)
|*
|*/

require_once('../classes/class.MainModule.php');

$DOC_SOURCES = array(
	1=>array(
		'id_offset'=>0,
		'name'=>"Ziņas",
		),
	2=>array(
		'id_offset'=>0,
		'name'=>"Recenzijas",
		),
	3=>array(
		'id_offset'=>10000,
		'name'=>"Forums",
		),
	);

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$search_log = post('spam') ? false : true;
	$search_q = post('search_q');
	if(get_magic_quotes_gpc())
		$search_q = stripslashes($search_q);
} else {
	$search_log = false;
	$search_q = urldecode(get('search_q'));
}

$special_search_q = urlencode($search_q);
$ent_search_q = ent($search_q);

$search_msg = array();

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_file('FILE_search', 'tmpl.search.php');
$template->set_title("Meklēšana: $ent_search_q");
$template->copy_block('BLOCK_middle', 'FILE_search');

if($search_q && (mb_strlen($search_q) < 3))
	$search_msg[] = "Kļūda: jāievada vismaz 3 simbolus";

$template->set_var("doc_count", 0, 'BLOCK_search');
$template->set_var('search_q', $ent_search_q);
$template->set_var('search_q_name', ": $ent_search_q");

if(mb_strlen($search_q) > 2)
{
	include("search/search.inc.php");
} else {
	$template->enable('BLOCK_search_help');
	$template->set_var('section_article_checked', ' checked="checked"', 'BLOCK_middle');
	$template->set_var('section_reviews_checked', ' checked="checked"', 'BLOCK_middle');
	$template->set_var('section_forum_checked', ' checked="checked"', 'BLOCK_middle');
}

if($search_msg)
{
	$template->enable('BLOCK_search');
	$template->enable('BLOCK_search_msg');
	foreach($search_msg as $msg)
	{
		$template->set_var('search_msg', $msg, 'BLOCK_search_msg');
		$template->parse_block('BLOCK_search_msg', TMPL_APPEND);
	}
}

$path = array('archive'=>array('module_id'=>'search', 'module_name'=>'MEKLĒT'));

$template->set_right();
$template->set_search($ent_search_q);
$template->set_reviews();
$template->set_poll();
$template->set_online();

$template->out();


