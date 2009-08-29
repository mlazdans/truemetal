<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/MainModule.php');
require_once('lib/Article.php');
require_once('lib/Calendar.php');

$y = array_shift($sys_parameters);
$m = array_shift($sys_parameters);
$d = array_shift($sys_parameters);

$calendar = new Calendar();
$calendar->parse_date($y, $m, $d);

$date = "$y-$m-$d 23:59:59";
$date_f = date("d.m.Y", mktime(0,0,0, $m, $d, $y));

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title("Arhīvs $date_f");
$template->set_file('FILE_archive', 'archive.tpl');
$template->copy_block('BLOCK_middle', 'FILE_archive');

$article = new Article();
$arts = $article->load(array(
	'end_date'=>$date,
	'order'=>'art_entered DESC',
	'limit'=>'50',
	));

// ja ir kaadi ieraksti shajaa datumaa, paraadam
// ja nee, tad paraadam attieciigu pazinjojumu
if(count($arts))
	$template->enable('BLOCK_archive_items');
else
	$template->enable('BLOCK_no_archive');

$old_date = '';
foreach($arts as $item)
{
	$date = date('m.Y', strtotime($item['art_entered']));
	if($old_date && ($old_date != $date))
	{
		$template->enable('BLOCK_archive_sep');
	} else {
		$template->disable('BLOCK_archive_sep');
	}

	if($old_date != $date)
	{
		$art_date = strftime('%B %Y', strtotime($item['art_entered']));
		$art_date = mb_convert_case($art_date, MB_CASE_TITLE);
		$template->enable('BLOCK_archive_date');
		$template->set_var('art_date', $art_date, 'BLOCK_archive_items');
		$template->parse_block('BLOCK_archive_date');
		$old_date = $date;
	} else {
		$template->disable('BLOCK_archive_date');
	}


	$template->set_var('art_id', $item['art_id'], 'BLOCK_archive_items');
	$template->set_var('art_name', $item['art_name'], 'BLOCK_archive_items');
	$template->set_var('art_module_id', $item['module_id'], 'BLOCK_archive_items');
	$template->parse_block('BLOCK_archive_items', TMPL_APPEND);
}

$template->set_right();
$template->set_calendar($y, $m, $d);
$template->set_poll();
$template->set_online();

$template->out();

