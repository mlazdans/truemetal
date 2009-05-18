<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

$editor = array_shift($sys_parameters);
$filter = array_shift($sys_parameters);

$filters = array(
	''=>'Visi',
	'image'=>'Attēli',
	'doc'=>'Dokumenti'
);

$patterns = array(
	''=>'/.*/',
	'image'=>'/(jpg|png|gif|jpeg)$/i',
	'doc'=>'/(rtf|doc|xls|txt|html)$/i'
);

if(!isset($filters[$filter]) || !isset($patterns[$filter]))
	die('Unknown filter - '.$filter);

$template = new Template($sys_template_root.'/admin/editor');
$template->set_file("FILE_index", 'tmpl.getfile.php');

$template->set_var('encoding', $sys_encoding);
$template->set_var('http_root', $sys_http_root);
$template->set_var('editor', $editor);
$template->set_var('filter', $filter);

if($filter == 'image')
	$template->enable('BLOCK_image');

// ustaadam filtrus
foreach($filters as $k=>$v) {
	$template->set_var('filter_id', $k);
	$template->set_var('filter_name', $v);
	if($filter == $k)
		$template->set_var('filter_selected', ' selected');
	else
		$template->set_var('filter_selected', '');
		
	$template->parse_block('BLOCK_filters', TMPL_APPEND);
}

$ts = time();
# 2month
$old_tresh = 3600 * 24 * 30;
if($dir = @opendir($sys_upload_root)) {
	$files = array();
	while(false !== ($file = readdir($dir)))
	{
		if ($file != "." && $file != ".." && preg_match($patterns[$filter], $file))
		{
			# display only 2month
			$mt = filemtime("$sys_upload_root/$file");
			if(($mt !== FALSE) && ($ts - $mt > $old_tresh))
			{
				continue;
			}
			$files[] = $file;
		}
	}

	$c = 0;
	natcasesort($files);
	foreach($files as $file) {
		++$c;
		$template->set_var('nr', $c);
		$template->set_var('file_name', $file);
		$template->parse_block('BLOCK_file', TMPL_APPEND);
	}
} else
	print '<b>$sys_upload_dir</b> definēto direktoriju nevar atvērt!'." [$sys_upload_dir]";

print $template->parse_file('FILE_index');

?>