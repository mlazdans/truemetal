<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/sphinxapi.php');
require_once('lib/Article.php');
require_once('lib/Forum.php');

function search(Array $params)
{
	global $DOC_SOURCES;

	extract($params);

	$special_search_q = urlencode($search_q);

	# Init classes
	$Article = new Article();
	$Forum = new Forum();

	# Sphinx
	$spx = new SphinxClient();
	$spx->SetConnectTimeout(1);
	$spx->SetLimits(0, $spx_limit);
	$spx->SetServer('localhost', 3313);
	$spx->SetSortMode(SPH_SORT_ATTR_DESC, "doc_comment_lastdate");

	if($sources){
		$spx->SetFilter('doc_source_id', $sources);
	}

	if(($res = $spx->Query($spx->EscapeString($search_q), $index)) === false){
		return array(
			'result'=>false,
			'res'=>array(),
			'spx'=>$spx,
			);
	} elseif(!empty($res["matches"])) {
		$ids = array();
		foreach($res["matches"] as $id=>$doc){
			$doc_source_id = $doc['attrs']['doc_source_id'];
			$doc_res_id = $doc['attrs']['doc_res_id'];
			$ids[$doc_source_id][] = $doc_res_id;
		}

		$items = array();
		foreach($ids as $doc_source_id=>$res_ids){
			$ds = $DOC_SOURCES[$doc_source_id];

			# Articles & reviews
			if( ($doc_source_id == 1) || ($doc_source_id == 2) )
			{
				$arts = $Article->load(array(
					'res_ids'=>$res_ids,
					'order'=>'r.res_comment_lastdate DESC',
					));

				foreach($arts as $item)
				{
					$doc_id = $item['res_id'];
					$items[$doc_id] = array(
						'doc_id'=>$item['res_id'],
						'doc_res_id'=>$item['art_id'],
						'doc_name'=>$item['art_name'],
						'doc_url'=>Article::Route($item)."?hl=$special_search_q",
						'doc_module_name'=>$ds['name'],
						'doc_date'=>date('d.m.Y', strtotime($item['art_entered'])),
						'doc_comment_lastdate'=>date('d.m.Y', strtotime($item['res_comment_lastdate'])),
						'doc_comment_count'=>$item['res_comment_count'],
						);
				}
				unset($arts);
			}

			# Forum
			if($doc_source_id == 3)
			{
				$forums = $Forum->load(array(
					'res_ids'=>$res_ids,
					'order'=>'r.res_comment_lastdate DESC',
					));
				foreach($forums as $item)
				{
					$doc_id = $item['res_id'];
					$items[$doc_id] = array(
						'doc_id'=>$item['res_id'],
						'doc_res_id'=>$item['forum_id'],
						'doc_name'=>$item['forum_name'],
						'doc_url'=>Forum::Route($item)."?hl=$special_search_q",
						'doc_module_name'=>$ds['name'],
						'doc_date'=>date('d.m.Y', strtotime($item['forum_entered'])),
						'doc_comment_lastdate'=>date('d.m.Y', strtotime($item['res_comment_lastdate'])),
						'doc_comment_count'=>$item['res_comment_count'],
						);
				}
				unset($forums);
			}
		}

		return array(
			'result'=>true,
			'res'=>$res,
			'items'=>$items,
			'spx'=>$spx,
			);
	} else {
		return array(
			'result'=>true,
			'res'=>$res,
			'items'=>array(),
			'spx'=>$spx,
			);
	}
}

