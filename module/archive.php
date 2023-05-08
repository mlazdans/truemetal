<?php declare(strict_types = 1);

$template = new MainModule($sys_module_id);
$template->set_title("Arhīvs: visi notikumi līdz šim");
$template->set_descr("Metāliskais arhīvs");

$T = $template->add_file('archive.tpl');

$arts = $db->Execute("SELECT * FROM view_mainpage");

# ja ir kaadi ieraksti shajaa datumaa, paraadam
# ja nee, tad paraadam attieciigu pazinjojumu
if(count($arts))
	$T->enable('BLOCK_archive_items');
else
	$T->enable('BLOCK_no_archive');

$old_date = '';
$formatter = new IntlDateFormatter("lv", IntlDateFormatter::SHORT, IntlDateFormatter::NONE);

foreach($arts as $item)
{
	$date = date('m.Y', strtotime($item['art_entered']));
	if($old_date && ($old_date != $date))
	{
		$T->enable('BLOCK_archive_sep');
	} else {
		$T->disable('BLOCK_archive_sep');
	}

	if($old_date != $date)
	{
		// $art_date = strftime('%B %Y', strtotime($item['art_entered']));
		$art_date = $formatter->format(strtotime($item['art_entered']));
		$art_date = mb_convert_case($art_date, MB_CASE_TITLE);
		$T->enable('BLOCK_archive_date');
		$T->set_var('art_date', $art_date);
		$T->parse_block('BLOCK_archive_date');
		$old_date = $date;
	} else {
		$T->disable('BLOCK_archive_date');
	}

	$T->set_var('art_id', $item['art_id']);
	$T->set_var('art_name', $item['art_name']);
	$T->set_var('art_module_id', $item['module_id']);
	$T->set_var('art_name_urlized', rawurlencode(urlize($item['art_name'])));
	$T->parse_block('BLOCK_archive_items', TMPL_APPEND);
}

$template->set_right_defaults();
$template->out($T);

