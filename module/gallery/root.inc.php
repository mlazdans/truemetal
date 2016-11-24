<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	$template->enable('BLOCK_not_loged');
	return;
}

# ja skataas bildi, nocheko vai attieciigaa galerija ir pieejama
if($gal_id)
{
	if($gal_id == 'view' && $gd_id)
	{
		$galdata = $GD->load($gd_id);
		if(!isset($galdata['gal_id'])) {
			header("Location: $module_root/");
			exit;
		}
		$gal = $gallery->load($galdata['gal_id']);
	} else {
		$gal = $gallery->load($gal_id);
	}

	if(!isset($gal['gal_id'])) {
		header("Location: $module_root/");
		exit;
	}

	$gal_name = "";
	if($gal['gal_ggid'])
		$gal_name .= "$gal[gg_name] / ";
	$gal_name .= "$gal[gal_name]";

	$template->set_var('gal_name', $gal_name);
	$template->set_var('gal_id', $gal['gal_id']);
	$template->set_title('Galerija '.$gal_name);

	if($gal['gal_ggid']){
		$template->set_var('gal_jump_id', "gg_".$gal['gal_ggid']);
	} else {
		$template->set_var('gal_jump_id', "gal_".$gal['gal_id']);
	}

	if($gal_id == 'view')
	{
		include('view.inc.php');
	} else {
		include('thumb_list.inc.php');
	}
} else {
	include('list.inc.php');
}

