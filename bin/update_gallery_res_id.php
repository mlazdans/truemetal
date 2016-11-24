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

require_once('lib/Gallery.php');
require_once('lib/GalleryData.php');

/*
$sql = "SELECT res_id FROM `gallery_data` WHERE gd_galid=166";
$data = $db->Execute($sql);
foreach($data as $item){
	$sql = "UPDATE res SET login_id = 80 WHERE res_id = $item[res_id]";
	$db->Execute($sql);
}
*/

/*
$gallery = new Gallery;
$data = $gallery->load();
foreach($data as $item){
	if($item['res_id']){
		$res_id = $item['res_id'];
	} else {
		$res_id = $gallery->Add();
	}
	$sql = "UPDATE gallery SET res_id = $res_id, login_id = 3 WHERE gal_id = $item[gal_id]";
	$sql = "UPDATE res SET login_id = 3 WHERE res_id = $res_id";
	$db->Execute($sql);
}
*/

$gallery = new GalleryData;
$data = $gallery->load(array(
	'gal_active'=>Res::STATE_ALL,
	'gd_visible'=>Res::STATE_ALL,
	));
foreach($data as $item){
	if($item['res_id']){
		$res_id = $item['res_id'];
		continue;
	} else {
		$res_id = $gallery->Add();
	}
	$sql = "UPDATE gallery_data SET res_id = $res_id WHERE gd_id = $item[gd_id]";
	$db->Execute($sql);

	$sql = "UPDATE res SET login_id = 3 WHERE res_id = $res_id";
	$db->Execute($sql);
}

