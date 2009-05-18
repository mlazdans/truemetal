<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//
$table = array_shift($sys_parameters);
$key = array_shift($sys_parameters);
$action = array_shift($sys_parameters);
$filter = array_shift($sys_parameters);

print '<html>
<head>
<link rel="stylesheet" type="text/css" href="'.$sys_http_root.'/styles.css">
<meta http-equiv="Content-Type" content="text/html; charset='.$sys_encoding.'">
</head>
<body>';

if($table == 'gallery_group' && $key) {
	require_once('../classes/class.GalleryGroup.php');
	$gallery_group = new GalleryGroup;
	$gg = $gallery_group->load($key);
	if($action == 'filter')
		remove_shit($gg['gg_data'], $filter);

	print $gg['gg_data'];
}

if($table == 'gallery' && $key) {
	require_once('../classes/class.Gallery.php');
	$gallery = new Gallery;
	$gal = $gallery->load($key);
	if($action == 'filter')
		remove_shit($gal['gal_data'], $filter);

	print $gal['gal_data'];
}

if($table == 'modules' && $key) {
	require_once('../classes/class.Module.php');
	$module = new Module;
	$module->load($key);
	$item = $module->get_item($key);
	if($action == 'filter')
		remove_shit($item['module_data'], $filter);

	print $item['module_data'];
}

if($table == 'articles' && $key) {
	require_once('../classes/class.Article.php');
	$article = new Article;
	$item = $article->load($key, 0, ARTICLE_ALL, ARTICLE_ALL);
	if($action == 'filter')
		remove_shit($item['art_data'], $filter);

	print $item['art_data'];
}

print '</body></html>';

?>