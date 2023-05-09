<?php declare(strict_types = 1);

//$sql_cache = 'SQL_NO_CACHE';

$template = new MainModule($sys_module_id, 'index.tpl');
$T = $template->add_file('search/log.tpl');
$template->set_title("Ko mēs meklējam");
$template->set_descr("Metāliskais meklētājs");

$T->enable('BLOCK_search_log');

$sql = "SELECT DISTINCT sl_q FROM `search_log` ORDER BY `sl_id` DESC LIMIT 0,200";
$q = $db->Query($sql);
while($r = $db->FetchAssoc($q))
{
	// my_strip_tags($r['sl_q']);
	$T->set_array(specialchars($r));
	$T->set_var('sl_q', $r['sl_q']);
	$T->set_var('sl_q_parsed', urlencode($r['sl_q']));
	$T->parse_block('BLOCK_search_log', TMPL_APPEND);
}

$template->set_right_defaults();
$template->out($T);
