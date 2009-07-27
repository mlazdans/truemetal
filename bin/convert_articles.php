<?php

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
	//'merge-divs' => true,
	//'merge-spans' => true,
	);

function art_clean($data)
{
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

	$tidy = tidy_parse_string($template, $GLOBALS['tidy_config'], 'UTF8');
	$tidy->cleanRepair();

	$doc = new DOMDocument();
	$doc->loadHTML($tidy);
	$xml = simplexml_import_dom($doc);

	# <a id= name=
	$els = $xml->xpath("//a[@name] | a[@id]");
	foreach($els as $el)
		unset($el['name'], $el['id']);

	# <td align=middle // Dažiem rakstiem
	$els = $xml->xpath("//td[@align='middle']");
	foreach($els as $el)
		unset($el['align']);

	# <ol type // Dažiem rakstiem
	$els = $xml->xpath("//ol[@type]");
	foreach($els as $el)
		unset($el['type']);

	# <img border= align=
	# <table border= align=
	$els = $xml->xpath("//img | //table");
	foreach($els as $el)
		unset($el['border'], $el['align']);

	# <a target=
	$els = $xml->xpath("//a");
	foreach($els as $el)
		unset($el['target']);

	return $xml;
} // art_clean

$i_am_admin = true;
require_once('../includes/inc.dbconnect.php');
require_once('../includes/inc.utils.php');

$db->AutoCommit(false);
$db->Execute("TRUNCATE `article`;");

// WHERE `art_id`=288
$sql = "SELECT * FROM `article_lv_old` ORDER BY `art_id`";
$items = $db->Execute($sql);

$data = '';
foreach($items as $item)
{
	$parts = preg_split('/<hr(.*)>/imsU', $item["art_data"]);
	$art_intro = array_shift($parts);
	$art_data = join("", $parts);

	$data .= "<div id=\"art_intro$item[art_id]\">$art_intro</div>\n";
	$data .= "<div id=\"art_data$item[art_id]\">$art_data</div>\n";
}

$xml = art_clean($data);

foreach($items as $item)
{
	$item_new = $item;

	$els = $xml->xpath("//div[@id='art_intro$item[art_id]']");
	$item_new['art_intro'] = $els[0]->asXML();

	$els = $xml->xpath("//div[@id='art_data$item[art_id]']");
	$item_new['art_data'] = $els[0]->asXML();

	$sql = "INSERT INTO `article` (".join(",", array_keys($item_new)).")";
	$val = $db->QuoteArray(array_values($item_new));
	$sql .= "VALUES ('".join("','", $val)."')";
	$db->Execute($sql);
}

$db->Commit();

print $xml->asXML();

