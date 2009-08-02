<?php

require_once('../classes/sphinxapi.php');
require_once('../classes/class.Article.php');
require_once('../classes/class.Forum.php');

# Init classes
$Article = new Article();
$Forum = new Forum();

$sections = post('sections', array());

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
} elseif($res["total_found"] == 0) {
	$search_msg[] = "Nekas netika atrasts";
} else {
	//if ( $cl->GetLastWarning() )
	//	print "WARNING: " . $cl->GetLastWarning() . "\n\n";
	#
	$ids = array();
	foreach($res["matches"] as $id=>$doc)
	{
		$doc_source_id = $doc['attrs']['doc_source_id'];
		$doc_real_id = $doc['attrs']['doc_real_id'];
		$ids[$doc_source_id][$id] = $doc_real_id;
	}

	$items_temp = array();
	foreach($ids as $doc_source_id=>$res_ids)
	{
		$ds = $DOC_SOURCES[$doc_source_id];

		# Articles & reviews
		if( ($doc_source_id == 1) || ($doc_source_id == 2) )
		{
			$arts = $Article->load(array(
				'art_ids'=>$res_ids,
				));
			foreach($arts as $item)
			{
				$doc_id = $item['art_id'] + $ds['id_offset'];
				$items_temp[$doc_id] = array(
					'doc_real_id'=>$item['art_id'],
					'doc_name'=>$item['art_name'],
					'doc_url'=>"/".($doc_source_id == 1 ? "article" : "reviews")."/$item[art_id]/?hl=$special_search_q",
					'doc_module_name'=>$DOC_SOURCES[$doc_source_id]['name'],
					);
			}
			unset($arts);
		}

		# Forum
		if($doc_source_id == 3)
		{
			$forums = $Forum->load(array(
				'forum_ids'=>$res_ids,
				));
			foreach($forums as $item)
			{
				$doc_id = $item['forum_id'] + $ds['id_offset'];
				$items_temp[$doc_id] = array(
					'doc_real_id'=>$item['forum_id'],
					'doc_name'=>$item['forum_name'],
					'doc_url'=>"/forum/$item[forum_id]/?hl=$special_search_q",
					'doc_module_name'=>$DOC_SOURCES[$doc_source_id]['name'],
					);
			}
			unset($forums);
		}
	}

	$items = array();
	foreach($res["matches"] as $id=>$doc)
		$items[$id] = $items_temp[$id];

	$template->set_var("doc_count", $res["total_found"], 'BLOCK_search');
}

# Docs
if(isset($items) && $items)
{
	$template->enable('BLOCK_search');
	$template->enable('BLOCK_search_item');
	foreach($items as $item)
	{
		$template->set_var('doc_url', $item['doc_url'], 'BLOCK_search_item');
		$template->set_var('doc_name', $item['doc_name'], 'BLOCK_search_item');
		$template->set_var('doc_module_name', $item['doc_module_name'], 'BLOCK_search_item');
		$template->parse_block('BLOCK_search_item', TMPL_APPEND);
	}
}

