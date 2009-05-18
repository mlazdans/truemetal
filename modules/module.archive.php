<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.MainModule.php');
require_once('../classes/class.Article.php');
require_once('../classes/class.Calendar.php');

$y = array_shift($sys_parameters);
$m = array_shift($sys_parameters);
$d = array_shift($sys_parameters);
$calendar = new Calendar;
$calendar->parse_date($y, $m, $d);

$date = "$y-$m-$d 23:59:59";

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title('Arhīvs');
$template->set_file('FILE_archive', 'tmpl.archive.php');
$template->copy_block('BLOCK_middle', 'FILE_archive');

$article = new Article();
$article->set_date_format('%Y:%m:%d:%H:%i');
//$this->date_format = '%Y:%m:%e:%H:%i';
$arts = $article->load_date($date);

// ja ir kaadi ieraksti shajaa datumaa, paraadam
// ja nee, tad paraadam attieciigu pazinjojumu
if(count($arts))
	$template->enable('BLOCK_archive_items');
else
	$template->enable('BLOCK_no_archive');

$old_date = '';
foreach($arts as $item) {
	//$item['art_date'] = substr($item['art_date'], 0, 10).':00:00:00';   // gads:meen:diena
	//print $item['art_date']."<br>";
	if($old_date != $item['art_entered']) {
		$template->enable('BLOCK_archive_date');
		$template->set_var('art_date', proc_date($item['art_entered']));
		$template->parse_block('BLOCK_archive_date');

		$old_date = $item['art_entered'];
	} else
		$template->disable('BLOCK_archive_date');

	$template->set_var('art_name', $item['art_name']);
	$template->set_var('art_id', $item['art_id']);
	$template->parse_block('BLOCK_archive_items', TMPL_APPEND);
}

$path = array('archive'=>array('module_id'=>'archive', 'module_name'=>'ARHĪVS'));

$template->set_right();
$template->set_calendar($y, $m, $d);
$template->set_poll();
$template->set_online();

$template->out();

?>