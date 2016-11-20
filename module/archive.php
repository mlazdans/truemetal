<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Article.php');

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_title("Arhīvs: visi notikumi līdz šim");
$template->set_file('FILE_archive', 'archive.tpl');
$template->copy_block('BLOCK_middle', 'FILE_archive');
$template->set_descr("Metāliskais arhīvs: visi notikumi līdz šim");

$arts = $db->Execute("SELECT * FROM view_mainpage");

# ja ir kaadi ieraksti shajaa datumaa, paraadam
# ja nee, tad paraadam attieciigu pazinjojumu
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
	$template->set_var('art_name_urlized', rawurlencode(urlize($item['art_name'])), 'BLOCK_archive_items');
	$template->parse_block('BLOCK_archive_items', TMPL_APPEND);
}

$template->set_right();
$template->set_login();
$template->set_online();

$template->out();

