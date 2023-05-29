<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

error_reporting(E_ALL);

$sql = "SELECT * FROM `article` ORDER BY `art_id`";
$items = $db->Execute($sql);

//$patt = '/"http:\/\/truemetal.lv(\/[^"]*)/';
$patt = '/"http:\/\/metal.id.lv(\/[^"]*)/';
foreach($items as $item){
	$d = $i = false;
	if(preg_match_all($patt, $item['art_data'], $m)){
		$item['art_data'] = preg_replace($patt, '"$1',  $item['art_data']);
		$d = true;
	}

	if(preg_match_all($patt, $item['art_intro'], $m)){
		$item['art_intro'] = preg_replace($patt, '"$1',  $item['art_intro']);
		$i = true;
	}

	if($d || $i){
		$sql = sprintf(
			"UPDATE `article` SET art_data = '%s', art_intro = '%s' WHERE art_id = %d",
			$db->Quote($item['art_data']),
			$db->Quote($item['art_intro']),
			$item['art_id']
			);
		//print "$sql\n\n";
		if(!$db->Execute($sql)){
			die();
		}
	}
}

