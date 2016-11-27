<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/ResComment.php');
require_once('lib/Gallery.php');
require_once('lib/GalleryData.php');
require_once('lib/MainModule.php');

$CACHE_ENABLE = true;
if($i_am_admin){
	//$CACHE_ENABLE = false;
}
$gal_id = array_shift($sys_parameters);
$gd_id = (int)array_shift($sys_parameters);
$hl = rawurldecode(get("hl"));
$action = post('action');

# thumbs per row
$tpr = 5;

$GD = new GalleryData;
$gallery = new Gallery;

if(($gal_id == 'thumb' || $gal_id == 'image') && $gd_id && user_loged())
{
	include('gallery/image.inc.php');
} else {
	$template = new MainModule($sys_module_id);
	$template->set_file('FILE_gallery', 'gallery.tpl');
	$template->copy_block('BLOCK_middle', 'FILE_gallery');

	include('gallery/root.inc.php');

	$template->set_right_defaults();
	$template->out();
}

