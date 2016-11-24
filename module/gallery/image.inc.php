<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

if($CACHE_ENABLE)
	$hash = cache_hash($gd_id.$gal_id.".jpg");

if($CACHE_ENABLE && cache_exists($hash)){
	$jpeg = cache_read($hash);
} else {
	$data = $GD->load(array(
		'gd_id'=>$gd_id,
		'load_images'=>true,
		));

	$jpeg = $gal_id == 'image' ? $data['gd_data'] : $data['gd_thumb'];

	if($CACHE_ENABLE && $jpeg)
		cache_save($hash, $jpeg);
}

header("Content-type: image/jpeg");
print $jpeg;

