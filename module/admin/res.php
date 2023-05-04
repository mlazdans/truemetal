<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/search.php');

$action = array_shift($sys_parameters);
$q = get('q');

if($action == 'search'){
	$items = array();
	$params = array(
		'spx_limit'=>250,
		'index'=>'doc_titles',
		'sources'=>array(),
		'search_q'=>$q,
		);

	$res = search($params);
	if($res['result'] === false){
		$search_msg[] = "Kļūda: ".$res['spx']->GetLastError();
	} elseif($res['res']['total_found'] == 0) {
		$search_msg[] = "Nekas netika atrasts";
	} else {
		$items = $res['items'];
	}

	$jsonData = new StdClass;
	$jsonData->data = $items;
	header('Content-Type: text/javascript; charset='.$sys_encoding);
	print json_encode($jsonData);
}
