<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// filtreejam visaadu zarazu (FrontPage, Office)

if(!isset($action))
	return;

if($action == 'filter') {
	//print_r($_POST);
	//die();
	$data = isset($_POST['data']) ? $_POST['data'] : array();
	/*
	$mod_id = isset($data['mod_id']) ? $data['mod_id'] : 0;
	$art_id = isset($data['art_id']) ? $data['art_id'] : 0;
	if(!$mod_id && !$art_id) {
		header("Location: $module_root/");
		exit;
	}*/
	$template->set_var('editor_filter', 'filter/');
	$template->set_var('editor_filter_level', isset($data['filter_data']) ? (integer)$data['filter_data'] : 0);
}

?>