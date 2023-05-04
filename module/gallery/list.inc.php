<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# ielasam galerijas
$template->set_title('Galerijas');
$gal_cache = "templates/gallery/gallery.html";
if(false && file_exists($gal_cache)) {
	$template->enable('BLOCK_gallery_list');
	$data = join('', file($gal_cache));
	$template->set_block_string('BLOCK_gallery_list', $data);
} elseif($data = $gallery->load()) {
	$template->enable('BLOCK_gallery_list');

	$data2 = array();
	foreach($data as $gal) {
		$k = empty($gal['gal_ggid']) ? "e-".$gal['gal_id'] : $gal['gal_ggid'];
		$data2[$k][] = $gal;
	}

	foreach($data2 as $gal_ggid=>$data)
	{
		$template->set_array($data[0], 'BLOCK_gallery_list');
		if($data[0]['gal_ggid']){
			$template->set_var('gg_name', $data[0]['gg_name'], 'BLOCK_gallery_group');
			$template->set_var('gal_jump_id', "gg_".$data[0]['gg_id'], 'BLOCK_gallery_group');
		} else {
			$template->set_var('gg_name', $data[0]['gal_name'], 'BLOCK_gallery_group');
			$template->set_var('gal_jump_id', "gal_".$data[0]['gal_id'], 'BLOCK_gallery_group');
		}

		foreach($data as $gal){
			$template->set_array($gal, 'BLOCK_gallery_data');
			$template->parse_block('BLOCK_gallery_data', TMPL_APPEND);
		}
		$template->parse_block('BLOCK_gallery_list', TMPL_APPEND);
	}
	/*
	if(false){
		save_data($gal_cache, $template->get_block('BLOCK_gallery')->parse());
	}
	*/
}

