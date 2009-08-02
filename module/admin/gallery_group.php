<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/AdminModule.php');
require_once('lib/GalleryGroup.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$gg_id = array_shift($sys_parameters);

$template = new AdminModule($sys_template_root.'/admin', $admin_module);
$template->set_title('Admin :: galeriju grupas');

$gallery_group = new GalleryGroup;
if($gallery_group->error_msg) {
	gallery_group_error($gallery_group->error_msg, $template);
	$template->out();
	exit;
}

/* ------------------------------------------------------------------------- */

function gallery_group_error($msg, &$template) {
	$template->enable('BLOCK_gallery_group_error');
	$template->set_var('error_msg', $msg);
}

$actions = array('delete_multiple');

if(in_array($action, $actions)) {
	if($gallery_group->process_action($_POST, $action))
		if($gg_id)
			header("Location: $module_root/$gg_id/");
		else
			header("Location: $module_root/");
	exit;
}

/* cancel */
if($action == 'cancel') {
	header("Location: $module_root/");
	exit;
/* save */
} elseif($action == 'gg_save') {
	if($id = $gallery_group->save($gg_id, $_POST['data'])) {
		header("Location: $module_root/".$id.'/');
		exit;
	} else
		gallery_group_error($gallery_group->error_msg, $template);
} elseif($action == 'gg_new') {
// jauns
	$template->enable('BLOCK_gallery_group_edit');
	$template->set_var('gg_date', date('Y-m-d'));
// editor
	set_editor($template, 'gallery_group_editor', 'gallery_group', $gg_id);
} else {
	include('../includes/inc.editor_filter.php');
// saraksts
	if(!$gg_id) {
		$gallery_groups = $gallery_group->load();

		if(count($gallery_groups))
			$template->enable('BLOCK_gallery_group_list');

		$gallery_group_count = 0;
		foreach($gallery_groups as $item) {
			++$gallery_group_count;
			$template->set_var('gallery_group_nr', $gallery_group_count);
			$template->set_array($item);
			if(!$item['gg_name'])
				$template->set_var('gg_name', '-nezināms-');

			$template->set_var('gg_color_class', '');
			$template->parse_block('BLOCK_gallery_groups', TMPL_APPEND);
		} // foreach gallery_groups
		$template->set_var('gallery_group_count', $gallery_group_count);
	} else {
// redigeet
		// editor
		set_editor($template, 'gallery_group_editor', 'gallery_group', $gg_id);

		$gg = $gallery_group->load($gg_id);
		$template->enable('BLOCK_gallery_group_edit');

		$template->set_array($gg);
	}
} // action

/* ------------------------------------------------------------------------- */

$template->out();
