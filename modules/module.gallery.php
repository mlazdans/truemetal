<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$module_permissions = array('admin');

require_once('../classes/class.Gallery.php');
require_once('../classes/class.MainModule.php');

function gallery_error($msg, &$template) {
	$template->enable('BLOCK_gallery_error');
	$template->set_var('error_msg', $msg);
}

// predefines
$tpr = 4;

// params
$gal_id = array_shift($sys_parameters);
$gd_id = (integer)array_shift($sys_parameters);

$gallery = new Gallery;

if(($gal_id == 'thumb' || $gal_id == 'image') && $gd_id)
{
	$data = $gallery->load_data($gd_id);
	header('Content-type: '.$data['gd_mime']);
	if($gal_id == 'image')
		print($data['gd_data']);
	else
		print($data['gd_thumb']);
	exit;
}

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_gallery', 'tmpl.gallery.php');
$template->copy_block('BLOCK_middle', 'FILE_gallery');

if($gal_id)
{
	// ja skataas bildi, nocheko vai attieciigaa galerija ir pieejama
	if($gal_id == 'view' && $gd_id)
	{
		$data = $gallery->load_data($gd_id);
		if(!isset($data['gd_galid']))
		{
			header("Location: $module_root/");
			exit;
		}
		$gal = $gallery->load($data['gd_galid']);
	} else {
		$gal = $gallery->load($gal_id);
	}

	if(!isset($gal['gal_id']))
	{
		header("Location: $module_root/");
		exit;
	}
	// --

	$title = 'Galerija';
	if($gal['gg_name'] && $gal['gal_name'])
		$title .= ": $gal[gg_name], $gal[gal_name]";
	elseif($gal['gal_name'])
		$title .= ": $gal[gal_name]";

	$template->set_var('gal_name', $gal['gal_name']);
	$template->set_var('gal_id', $gal['gal_id']);
	if($gal_id == 'view')
	{
		$data = $gallery->load_data($gd_id);
		//printr($data);
		// ja skataas pa vienai
		$template->enable('BLOCK_image');
		// nechekojam, vai ir veel bildes
		$next_id = $gallery->get_next_data($gal['gal_id'], $gd_id);
		if($next_id)
		{
			$template->set_var('gd_nextid', $next_id);
			$template->enable('BLOCK_image_viewnext');
		} else {
			$template->enable('BLOCK_image_viewsingle');
		}
		// end check
		$template->set_var('gd_descr', $data['gd_descr'], 'BLOCK_image');
		$template->set_var('gd_pos', $data['gd_pos'], 'BLOCK_image');
		$template->set_var('gd_id', $gd_id, 'BLOCK_image');
		$title .= ", bilde $data[gd_pos]";
	} else {
		$template->enable('BLOCK_thumb_list');
		$gal_cache = "$sys_template_root/gallery/$gal_id.html";
		# Disable
		if(file_exists($gal_cache)) {
			$data = join('', file($gal_cache));
			$template->set_block_string('BLOCK_thumb', $data);
		} else {
			// ielasam thumbus
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
				$template->parse_block('BLOCK_thumb', TMPL_APPEND);
			}
		} // cache exists
	} // gal_id == view
	$template->set_title(ent(strip_tags($title)));
} else {
	// ielasam galerijas
	$template->set_title('Galerijas');
	$gal_cache = "$sys_template_root/gallery/gallery.html";
	# Disabled
	if(false && file_exists($gal_cache)) {
		$template->enable('BLOCK_gallery_list');
		$data = join('', file($gal_cache));
		$template->set_block_string('BLOCK_gallery', $data);
	} elseif($data = $gallery->load(0, GALLERY_ACTIVE, GALLERY_VISIBLE)) {
		$template->enable('BLOCK_gallery_list');
		$old_ggid2 = $old_ggid = -1;
		$gc = 0;
		foreach($data as $gal)
		{
			++$gc;
			if($gal['gal_ggid'])
			{
				$template->enable('BLOCK_gallery_padding');
				$template->enable('BLOCK_gallery_data_padding');
				$template->disable('BLOCK_gallery_nogroup');
				$template->disable('BLOCK_gallery_data_nogroup');
			} else {
				$template->disable('BLOCK_gallery_padding');
				$template->disable('BLOCK_gallery_data_padding');
				$template->enable('BLOCK_gallery_nogroup');
				$template->enable('BLOCK_gallery_data_nogroup');
			}

			//$test_data = trim(html_entity_decode(strip_tags(str_replace('&nbsp;', '', $gal['gal_data']))));
			// apraksts
			if($gal['gal_data'])
				$template->enable('BLOCK_gallery_data');
			else
				$template->disable('BLOCK_gallery_data');

			// grupas apraksts
			if($gal['gg_data'])
				$template->enable('BLOCK_gallery_group_data');
			else
				$template->disable('BLOCK_gallery_group_data');

			// jauna grupa
			if($old_ggid != $gal['gal_ggid'] && $gal['gal_ggid'])
				$template->enable('BLOCK_gallery_group');
			else
				$template->disable('BLOCK_gallery_group');

			$old_ggid2 = $old_ggid;
			$old_ggid = $gal['gal_ggid'];

			// nosleedzam galerijas grupu, ja taa nav pirmaa *galerija*, vai arii
			// vispaar nav grupa
			if((($old_ggid2 != $gal['gal_ggid']) || !$gal['gal_ggid']) && $gc != 1)
			{
				$template->enable('BLOCK_gallery_group_end');
			} else {
				$template->disable('BLOCK_gallery_group_end');
			}

			$gal['gg_name'] = ent($gal['gg_name']);
			$gal['gal_name'] = ent($gal['gal_name']);
			$template->set_array($gal);
			$template->parse_block('BLOCK_gallery', TMPL_APPEND);
		}
	} else {
		gallery_error($gallery->error_msg, $template);
	}
}

$template->set_right();
$template->set_poll();
$template->set_online();
$template->out();

