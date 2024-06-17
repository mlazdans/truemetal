<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

$tidy_config = array(
	'doctype' => 'strict',
	'join-styles' => true,
	'join-classes' => true,
	'clean' => true,
	'output-xhtml' => true,
	'newline' => 'LF',
	//'show-body-only' => true,
	'wrap' => 0,
	'alt-text' => '',
	'drop-font-tags' => true,
	'drop-proprietary-attributes' => true,
	'enclose-block-text' => true,
	'enclose-text' => true,
	'logical-emphasis' => true,
	'word-2000' => true,
	'merge-divs' => false,
	'merge-spans' => false,
	//'merge-divs' => true,
	//'merge-spans' => true,
);

function gal_clean($data)
{
	$patts = array(
		'/"http:\/\/truemetal.lv(\/[^"]*)/',
		'/"http:\/\/metal.id.lv(\/[^"]*)/',
		);

	foreach($patts as $patt){
		$data = preg_replace($patt, '"$1',  $data);
	}

	$template='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
</head>
<body>'.
$data.
'</body>'.
'</html>';

	$tidy_config = $GLOBALS['tidy_config'];
	$tidy_config['join-styles'] = false;
	$tidy_config['join-classes'] = false;
	$tidy_config['clean'] = false;
	$tidy = tidy_parse_string($template, $tidy_config, 'UTF8');
	$tidy->cleanRepair();

	$doc = new DOMDocument();
	$doc->loadHTML($tidy);
	return get_inner_html($doc->getElementsByTagName("p")->item(0));
} // gal_clean

$db->AutoCommit(false);

/*
$sql = "SELECT * FROM `gallery_group_old`";
$items = $db->Execute($sql);

foreach($items as $item)
{
	$data = $item['gg_data'];
	if($data)
		$data = gal_clean($data);
	$sql = "UPDATE `gallery_group_old` SET `gg_data` = '".$db->Quote($data)."' WHERE gg_id = $item[gg_id]";
	$db->Execute($sql);
}
$db->Commit();
*/
$sql = "SELECT * FROM `gallery_old`";
$items = $db->Execute($sql);

foreach($items as $item)
{
	$data = $item['gal_data'];
	if($data)
		$data = gal_clean($data);
	$sql = "UPDATE `gallery_old` SET `gal_data` = '".$db->Quote($data)."' WHERE gal_id = $item[gal_id]";
	$db->Execute($sql);
}
$db->Commit();

