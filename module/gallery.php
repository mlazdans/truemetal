<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Gallery.php');
require_once('lib/MainModule.php');

$CACHE_ENABLE = true;

function gallery_error($msg, &$template) {
	$template->enable('BLOCK_gallery_error');
	$template->set_var('error_msg', $msg);
}

# thumbs per row
$tpr = 4;
$gal_id = array_shift($sys_parameters);
$gd_id = (int)array_shift($sys_parameters);

$gallery = new Gallery;

if(($gal_id == 'thumb' || $gal_id == 'image') && $gd_id) {
	$gal_cache = "$sys_public_root/cache/".cache_hash($gd_id, $gal_id."_").".jpg";
	if($CACHE_ENABLE && file_exists($gal_cache)) {
		$jpeg = file_get_contents($gal_cache);
	} else {
		$data = $gallery->load_data($gd_id);
		if($gal_id == 'image')
			$jpeg = $data['gd_data'];
		else
			$jpeg = $data['gd_thumb'];
		save_data($gal_cache, $jpeg);
	}

	header('Content-type: image/jpeg');
	print $jpeg;
	exit;
}

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_gallery', 'gallery.tpl');
$template->copy_block('BLOCK_middle', 'FILE_gallery');

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	gallery_error("TrueMetal!", $template);
} else {
if($gal_id)
{
	# ja skataas bildi, nocheko vai attieciigaa galerija ir pieejama
	if($gal_id == 'view' && $gd_id) {
		$data = $gallery->load_data($gd_id);
		if(!isset($data['gd_galid'])) {
			header("Location: $module_root/");
			exit;
		}
		$gal = $gallery->load($data['gd_galid']);
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

	if($gal_id == 'view') {
		$data = $gallery->load_data($gd_id);
		# ja skataas pa vienai
		$template->enable('BLOCK_image');

		# nechekojam, vai ir veel bildes
		$next_id = $gallery->get_next_data($gal['gal_id'], $gd_id);
		if($next_id) {
			$template->set_var('gd_nextid', $next_id);
			$template->enable('BLOCK_image_viewnext');
		} else {
			$template->enable('BLOCK_image_viewsingle');
		}

		$template->set_var('gd_descr', $data['gd_descr']);
		$template->set_var('gd_id', $gd_id);
	} else {
		$template->enable('BLOCK_thumb_list');
		$gal_cache = "$sys_template_root/gallery/$gal_id.html";
		if(false && file_exists($gal_cache)) {
			$data = join('', file($gal_cache));
			$template->set_block_string('BLOCK_thumb', $data);
		} else {
			# ielasam thumbus
			$data = $gallery->load_data(0, $gal_id);
			$thumb_count = count($data);
			$c = 0;
			foreach($data as $thumb)
			{
				++$c;
				if($c % $tpr == 1)
					$template->enable('BLOCK_tr1');
				else
					$template->disable('BLOCK_tr1');
				if(($c % $tpr == 0) || ($c == $thumb_count))
					$template->enable('BLOCK_tr2');
				else
					$template->disable('BLOCK_tr2');
				$template->set_var('gd_id', $thumb['gd_id'], 'BLOCK_thumb');

				$hash = cache_hash($thumb['gd_id'], "thumb_");
				$gal_cache = "$sys_public_root/cache/$hash.jpg";
				if(file_exists($gal_cache)){
					$template->set_var('thumb_path', "$sys_http_root/cache/$hash.jpg", 'BLOCK_thumb');
				} else {
					$template->set_var('thumb_path', "$module_root/thumb/$thumb[gd_id]/", 'BLOCK_thumb');
				}

				$template->parse_block('BLOCK_thumb', TMPL_APPEND);
			}

			if(false){
				save_data($gal_cache, $template->get_block('BLOCK_thumb')->parse());
			}
		}
	}
} else {
	# ielasam galerijas
	$template->set_title('Galerijas');
	$gal_cache = "$sys_template_root/gallery/gallery.html";
	if(false && file_exists($gal_cache)) {
		$template->enable('BLOCK_gallery_list');
		$data = join('', file($gal_cache));
		$template->set_block_string('BLOCK_gallery_list', $data);
	} elseif($data = $gallery->load(0, GALLERY_ACTIVE, GALLERY_VISIBLE)) {
		$template->enable('BLOCK_gallery_list');
		$old_ggid = -1;

		$data2 = array();
		foreach($data as $gal) {
			$k = empty($gal['gal_ggid']) ? "e-".$gal['gal_id'] : $gal['gal_ggid'];
			$data2[$k][] = $gal;
		}

		$old_ggid = -1;
		foreach($data2 as $gal_ggid=>$data)
		{
			$template->set_array($data[0], 'BLOCK_gallery_list');
			if($data[0]['gal_ggid']){
				$template->set_var('gg_name', $data[0]['gg_name'], 'BLOCK_gallery_group');
			} else {
				$template->set_var('gg_name', $data[0]['gal_name'], 'BLOCK_gallery_group');
			}

			foreach($data as $gal){
				$template->set_array($gal, 'BLOCK_gallery_data');
				$template->parse_block('BLOCK_gallery_data', TMPL_APPEND);
			}
			$template->parse_block('BLOCK_gallery_list', TMPL_APPEND);
		}
		if(false){
			save_data($gal_cache, $template->get_block('BLOCK_gallery')->parse());
		}
	} else {
		gallery_error($gallery->error_msg, $template);
	}
}
}

$template->set_right();
$template->set_recent_forum();
$template->set_login();
$template->set_online();
$template->set_search();
$template->set_jubilars();
$template->out();
