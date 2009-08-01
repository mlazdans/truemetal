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
|*/

$DOC_SOURCES = array(
	1=>array(
		'name'=>"Ziņas",
		),
	2=>array(
		'name'=>"Recenzijas",
		),
	3=>array(
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

require_once('../classes/sphinxapi.php');
require_once('../classes/class.MainModule.php');
//require_once('../classes/class.Article.php');

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

if(mb_strlen($search_q) > 2)
{
	$sections = post('sections', array());
	$template->set_var('search_q', $ent_search_q);
	$template->set_var('search_q_name', ": $ent_search_q");

	# Log
	if($search_log)
	{
		$sql = sprintf("
INSERT INTO `search_log` (
	`sl_userid`, `sl_q`, `sl_ip`, `sl_entered`
) VALUES (
	%s, '%s', '%s', NOW()
);",
			isset($_SESSION['login']['l_id']) ? $_SESSION['login']['l_id'] : "NULL",
			addslashes($search_q),
			$_SERVER['REMOTE_ADDR']
			);
		$db->Execute($sql);
	}

	# Sphinx
	$cl = new SphinxClient();
	$cl->SetConnectTimeout(1);
	$cl->SetLimits(0, 100);
	$cl->SetMatchMode(SPH_MATCH_BOOLEAN);

	$sources = array();
	if(in_array('article', $sections))
	{
		$template->set_var('section_article_checked', ' checked="checked"', 'BLOCK_middle');
		$sources[] = 1;
	}
	if(in_array('reviews', $sections))
	{
		$template->set_var('section_reviews_checked', ' checked="checked"', 'BLOCK_middle');
		$sources[] = 2;
	}
	if(in_array('forum', $sections))
	{
		$template->set_var('section_forum_checked', ' checked="checked"', 'BLOCK_middle');
		$sources[] = 3;
	}

	$cl->SetSortMode(SPH_SORT_ATTR_DESC, "doc_entered");
	if($sources)
		$cl->SetFilter('doc_source_id', $sources);

	if(($res = $cl->Query($search_q, "doc")) === false)
	{
		$search_msg[] = "Kļūda: ".$cl->GetLastError();
		user_error($cl->GetLastError(), E_USER_WARNING);
	} else {
		$doc_ids = array_keys($res["matches"]);
		$sql = "SELECT doc_source_id, doc_real_id, doc_name FROM documents WHERE doc_id IN (".join($doc_ids, ",").")";
		$items = $db->Execute($sql);
		//printr($sql);
		//printr($res);
		//return;
		//if ( $cl->GetLastWarning() )
		//	print "WARNING: " . $cl->GetLastWarning() . "\n\n";
		$template->set_var("doc_count", $res["total_found"], 'BLOCK_search');

		/*
		if(isset($res["matches"]) && is_array($res["matches"]))
		{
			$art_ids = array_keys($res["matches"]);
			$Article = new Article();
			$items = $Article->load(array(
				'art_ids'=>$art_ids,
				));
		} else {
			$search_msg[] = "Nekas netika atrasts";
		}
		*/
	}
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

# Articles
if(isset($items) && $items)
{
	$template->enable('BLOCK_search');
	$template->enable('BLOCK_search_item');
	foreach($items as $item)
	{
		if($item['doc_source_id'] == 1)
			$doc_url = "/article/$item[doc_real_id]/?hl=$special_search_q";
		if($item['doc_source_id'] == 2)
			$doc_url = "/reviews/$item[doc_real_id]/?hl=$special_search_q";
		if($item['doc_source_id'] == 3)
			$doc_url = "/forum/$item[doc_real_id]/?hl=$special_search_q";
		$template->set_var('doc_url', $doc_url, 'BLOCK_search_item');
		$template->set_var('doc_name', $item['doc_name'], 'BLOCK_search_item');
		$template->set_var('doc_module_name', $DOC_SOURCES[$item['doc_source_id']]['name'], 'BLOCK_search_item');
		$template->parse_block('BLOCK_search_item', TMPL_APPEND);
	}
}


$path = array('archive'=>array('module_id'=>'search', 'module_name'=>'MEKLĒT'));

$template->set_right();
$template->set_search($ent_search_q);
$template->set_reviews();
$template->set_poll();
$template->set_online();

$template->out();


