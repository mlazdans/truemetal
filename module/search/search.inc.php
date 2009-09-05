<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/sphinxapi.php');
require_once('lib/Article.php');
require_once('lib/Forum.php');

# Init classes
$Article = new Article();
$Forum = new Forum();

$sections = post('sections', array());
$only_titles = post('only_titles', false);

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

if($only_titles)
{
	$template->set_var('only_titles_checked', ' checked="checked"', 'BLOCK_middle');
}

$cl->SetSortMode(SPH_SORT_ATTR_DESC, "doc_entered");
if($sources)
	$cl->SetFilter('doc_source_id', $sources);

$index = ($only_titles ? "doc_titles" : "doc");

if(($res = $cl->Query($search_q, $index)) === false)
{
	$search_msg[] = "Kļūda: ".$cl->GetLastError();
	user_error($cl->GetLastError(), E_USER_WARNING);
} elseif($res["total_found"] == 0) {
	$search_msg[] = "Nekas netika atrasts";
} else {
	//if ( $cl->GetLastWarning() )
	//	print "WARNING: " . $cl->GetLastWarning() . "\n\n";
	$ids = array();
	foreach($res["matches"] as $id=>$doc)
	{
		$doc_source_id = $doc['attrs']['doc_source_id'];
		$doc_real_id = $doc['attrs']['doc_real_id'];
		$ids[$doc_source_id][$doc_real_id] = $id;
	}

	$items_temp = array();
	foreach($ids as $doc_source_id=>$doc_ids)
	{
		$res_ids = array_keys($doc_ids);
		$ds = $DOC_SOURCES[$doc_source_id];

		# Articles & reviews
		if( ($doc_source_id == 1) || ($doc_source_id == 2) )
		{
			$arts = $Article->load(array(
				'art_ids'=>$res_ids,
				));
			foreach($arts as $item)
			{
				//$doc_id = $item['art_id'] + $ds['id_offset'.($only_titles ? "_titles" : "")];
				$doc_id = $doc_ids[$item['art_id']];
				$items_temp[$doc_id] = array(
					'doc_real_id'=>$item['art_id'],
					'doc_name'=>$item['art_name'],
					'doc_url'=>"/".($doc_source_id == 1 ? "article" : "reviews")."/$item[art_id]/?hl=$special_search_q",
					'doc_module_name'=>$ds['name'],
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
				//$doc_id = $item['forum_id'] + $ds['id_offset'.($only_titles ? "_titles" : "")];
				$doc_id = $doc_ids[$item['forum_id']];
				$items_temp[$doc_id] = array(
					'doc_real_id'=>$item['forum_id'],
					'doc_name'=>$item['forum_name'],
					'doc_url'=>"/forum/$item[forum_id]/?hl=$special_search_q",
					'doc_module_name'=>$ds['name'],
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

