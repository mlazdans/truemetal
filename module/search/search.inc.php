<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/search.php');

# TODO: kārtošana pēc datuma gan article, gan forum. Tagad kārtojas atsevišķi

$sections = post('sections', array());
$only_titles = post('only_titles', false);
$spx_limit = 250;
$index = ($only_titles ? "doc_titles" : "doc");

$sources = array();
if(in_array('article', $sections)){
	$T->set_var('section_article_checked', ' checked="checked"', 'BLOCK_middle');
	$sources[] = 1;
}
if(in_array('reviews', $sections)){
	$T->set_var('section_reviews_checked', ' checked="checked"', 'BLOCK_middle');
	$sources[] = 2;
}
if(in_array('forum', $sections)){
	$T->set_var('section_forum_checked', ' checked="checked"', 'BLOCK_middle');
	$sources[] = 3;
}
if($only_titles){
	$T->set_var('only_titles_checked', ' checked="checked"', 'BLOCK_middle');
}

$params = array(
	'spx_limit'=>$spx_limit,
	'index'=>$index,
	'sources'=>$sources,
	'search_q'=>$search_q,
	);

# Log
if($search_log)
{
	$sql = sprintf(
		"INSERT INTO search_log (
			login_id, sl_q, sl_ip, sl_entered
		) VALUES (
			%s, ?, ?, CURRENT_TIMESTAMP
		)",
		user_loged() ? (int)$_SESSION['login']['l_id'] : "NULL"
	);

	DB::Execute($sql, $search_q, $_SERVER['REMOTE_ADDR']);
}

$res = search($params);
if($res['result'] === false){
	$search_msg[] = "Kļūda: ".$res['spx']->GetLastError();
	user_error($res['spx']->GetLastError(), E_USER_WARNING);
} elseif($res['res']['total_found'] == 0) {
	$search_msg[] = "Nekas netika atrasts";
} else {
	$items = $res['items'];
	$T->set_var("doc_count", $res['res']['total_found'], 'BLOCK_search');
}

if($res['res']['total_found'] > $spx_limit){
	$search_msg[] = "Uzmanību: atrasti ".$res['res']['total_found']." rezultāti, rādam $spx_limit";
}

if(!empty($items))
{
	$T->enable('BLOCK_search');
	$T->enable('BLOCK_search_item');
	foreach($items as $item)
	{
		$T->set_array($item, 'BLOCK_search_item');
		$T->parse_block('BLOCK_search_item', TMPL_APPEND);
	}
}

