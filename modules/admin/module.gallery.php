<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('lib/AdminModule.php');
require_once('lib/Gallery.php');
require_once('lib/GalleryGroup.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$gal_id = array_shift($sys_parameters);

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: galerija');

$gallery = new Gallery();
if($gallery->error_msg) {
	gallery_error($gallery->error_msg, $template);
	$template->out();
	exit;
}

$gallery_group = new GalleryGroup;

/* ------------------------------------------------------------------------- */

function setup_gallery_groups(&$template, $gg_id = 0)
{
	global $gallery_group;

	$gallery_groups = $gallery_group->load();
	foreach($gallery_groups as $item)
	{
		if($gg_id == $item['gg_id'])
			$template->set_var('gg_selected', ' selected', 'BLOCK_gallery_groups');
		else
			$template->set_var('gg_selected', '', 'BLOCK_gallery_groups');
		$template->set_array($item, 'BLOCK_gallery_groups');
		$template->parse_block('BLOCK_gallery_groups', TMPL_APPEND);
	}
}

function gallery_error($msg, &$template) {
	$template->enable('BLOCK_gallery_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple', 'activate_multiple', 'deactivate_multiple', 'show_multiple', 'hide_multiple');

if(in_array($action, $actions)) {
	if($gallery->process_action($_POST, $action))
		if($gal_id)
			header("Location: $module_root/$gal_id/");
		else
			header("Location: $module_root/");
	exit;
}

/* cancel */
if($action == 'cancel') {
	header("Location: $module_root/");
	exit;
/* save */
} elseif($action == 'gal_save') {
	if($id = $gallery->save($gal_id, $_POST['data'])) {
		header("Location: $module_root/".$id.'/');
		exit;
	} else
		gallery_error($gallery->error_msg, $template);
} elseif($action == 'gal_new') {
// jauns
	$template->enable('BLOCK_gallery_edit');
	$template->enable('BLOCK_gallery_submit');
	setup_gallery_groups($template);
	// editor
	set_editor($template, 'gallery_editor', 'gallery', $gal_id);
} else {
	include('../includes/inc.editor_filter.php');
// saraksts
	if(!$gal_id) {
		$galleries = $gallery->load(0, GALLERY_ALL, GALLERY_ALL);

		if(count($galleries))
			$template->enable('BLOCK_gallery_list');

		$old_ggid = $gallery_count = 0;
		foreach($galleries as $item) {
			if($item['gal_ggid']) {
				$template->enable('BLOCK_gallery_padding');
				$template->disable('BLOCK_gallery_nopadding');
			} else {
				$template->disable('BLOCK_gallery_padding');
				$template->enable('BLOCK_gallery_nopadding');
			}

			++$gallery_count;
			$template->set_var('gallery_nr', $gallery_count, 'BLOCK_gallery_list');
			$template->set_array($item, 'BLOCK_gallery_list');

			if(!$item['gal_name'])
				$template->set_var('gal_name', '-nezināms-', 'BLOCK_gallery_list');

			$template->set_var('gal_color_class', '', 'BLOCK_gallery_list');
			if($item['gal_visible'] != GALLERY_VISIBLE)
				$template->set_var('gal_color_class', 'box-invisible', 'BLOCK_gallery_list');
			if($item['gal_active'] != GALLERY_ACTIVE)
				$template->set_var('gal_color_class', 'box-inactive', 'BLOCK_gallery_list');
			//ja neaktiivs un neredzams
			if($item['gal_active'] != GALLERY_ACTIVE && $item['gal_visible'] != GALLERY_VISIBLE)
				$template->set_var('gal_color_class', 'box-inactive-invisible', 'BLOCK_gallery_list');

			// galeriju grupa
			if($old_ggid != $item['gal_ggid'] && $item['gal_ggid'])
				$template->enable('BLOCK_gallery_group');
			else
				$template->disable('BLOCK_gallery_group');

			$old_ggid = $item['gal_ggid'];

			$template->parse_block('BLOCK_galleries', TMPL_APPEND);
		} // foreach galleries
		$template->set_var('gallery_count', $gallery_count, 'BLOCK_gallery_list');
	} else {
// redigeet
		$gal = $gallery->load($gal_id, GALLERY_ALL, GALLERY_ALL);
		$template->enable('BLOCK_gallery_edit');

		// editor
		set_editor($template, 'gallery_editor', 'gallery', $gal_id);

		$template->set_array($gal, 'BLOCK_gallery_edit');

		if($gal['gal_visible'] == GALLERY_VISIBLE)
			$template->set_var('gal_visible_y', ' selected', 'BLOCK_gallery_edit');
		else
			$template->set_var('gal_visible_n', ' selected', 'BLOCK_gallery_edit');

		if($gal['gal_active'] == GALLERY_ACTIVE)
			$template->set_var('gal_active_y', ' selected', 'BLOCK_gallery_edit');
		else
			$template->set_var('gal_active_n', ' selected', 'BLOCK_gallery_edit');

		setup_gallery_groups($template, $gal['gal_ggid']);

		// thumbs
		$template->set_file('FILE_gallery_data', 'tmpl.gallery_data.php');
		$template->set_var('http_root', $sys_http_root);
		$data = $gallery->load_data(0, $gal_id, GALLERY_DATA_VISIBLE, GALLERY_ACTIVE);
		if(count($data))
			$template->enable('BLOCK_gallery_thumbs');

		$c = 0;
		foreach($data as $item)
		{
			++$c;
			if($c % 8 == 1)
				$template->set_var('tr1', '<tr>', 'BLOCK_gallery_thumbs');
			else
				$template->set_var('tr1', '', 'BLOCK_gallery_thumbs');
			$template->set_var('gd_id', $item['gd_id'], 'BLOCK_gallery_thumbs');
			$template->set_var('gd_descr', $item['gd_descr'], 'BLOCK_gallery_thumbs');
			if($c % 8 == 0)
				$template->set_var('tr2', '</tr><tr><td colspan="8"><hr></td></tr>', 'BLOCK_gallery_thumbs');
			else
				$template->set_var('tr2', '', 'BLOCK_gallery_thumbs');
			$template->parse_block('BLOCK_gallery_thumbs', TMPL_APPEND);
		}
		if($c && ($c % 8 != 0))
			$template->set_var('tr2', '</tr>', 'BLOCK_gallery_thumbs');
		$template->copy_block('BLOCK_gallery_data', 'FILE_gallery_data');
	}
} // action

/* ------------------------------------------------------------------------- */

$template->out();

?>