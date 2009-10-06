<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//$sql_cache = 'SQL_NO_CACHE';
require_once('lib/MainModule.php');

$template = new MainModule($sys_template_root, $sys_module_id, 'index.tpl');
$template->set_file('FILE_search', 'search/log.tpl');
$template->set_title("Ko mēs meklējam");
$template->set_descr("Metāliskais meklētājs: ko tad mēs šeit meklējam?");
$template->copy_block('BLOCK_middle', 'FILE_search');

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

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_search();

$template->out();

