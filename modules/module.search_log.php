<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//$sql_cache = 'SQL_NO_CACHE';
require_once('../classes/class.MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_file('FILE_search', 'tmpl.search_log.php');
$template->set_title("Ko mēs meklējam");
$template->set_array($sys_lang_def, 'BLOCK_middle');
$template->copy_block('BLOCK_middle', 'FILE_search');

$path = array('archive'=>array('module_id'=>'search_log', 'module_name'=>'KO MĒS MEKLĒJAM'));


//$allowed = $i_am_admin || (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "google") !== FALSE);
$allowed = true;

if($allowed)
{
	$template->enable('BLOCK_search_log');

	$sql = "SELECT DISTINCT sl_q FROM `search_log` ORDER BY `sl_id` DESC LIMIT 0,100";
	$q = $db->Query($sql);
	while($r = $db->FetchAssoc($q))
	{
		$template->set_array($r, 'BLOCK_search_log');
		$template->set_array($r, 'BLOCK_search_log');
		$template->set_var('sl_q', $r['sl_q']);
		$template->set_var('sl_q_parsed', urlencode($r['sl_q']));
		$template->parse_block('BLOCK_search_log', TMPL_APPEND);
	}
} else {
	$template->enable('BLOCK_search_log_denied');
}

$template->set_right();
$template->set_search();
$template->set_reviews();
$template->set_poll();
$template->set_online();
$template->set_calendar();

$template->out();

