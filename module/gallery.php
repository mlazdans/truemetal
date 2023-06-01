<?php declare(strict_types = 1);

$CACHE_ENABLE = true;
if($i_am_admin){
	// $CACHE_ENABLE = false;
}

$template = new MainModule($sys_module_id);
$template->set_title('Galerijas');

if(!User::logged()){
	$template->not_logged();
	$template->set_right_defaults();
	$template->out(null);
	return;
}

$gal_id = array_shift($sys_parameters);
$gd_id = (int)array_shift($sys_parameters);
$hl = get("hl");
$action = post('action');

# TODO: atsevišķs TrueResponseInterface priekš bildēm
if(($gal_id == 'thumbs') && $gd_id){
	$gal_id = $gd_id;
	$gd_id = (int)array_shift($sys_parameters);
	if($gd_id){
		gallery_image($gd_id, 'thumb');
		return;
	}
	$template->not_found();
} elseif(($gal_id == 'thumb') && $gd_id){
	gallery_image($gd_id, 'thumb');
	return;
} elseif(($gal_id == 'image') && $gd_id) {
	gallery_image($gd_id, 'image');
	return;
} else {
	if($gal_id == 'view'){
		$T = gallery_view($template, $gd_id);
	} elseif($gal_id){
		$T = gallery_thumbs_list($template, (int)$gal_id);
	} else {
		$T = gallery_root($template);
	}
}

$template->set_right_defaults();
$template->out($T??null);
