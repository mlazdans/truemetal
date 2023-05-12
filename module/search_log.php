<?php declare(strict_types = 1);

//$sql_cache = 'SQL_NO_CACHE';

$template = new MainModule($sys_module_id);
$template->set_title("Ko mēs meklējam");
$template->set_descr("Metāliskais meklētājs");
$template->set_right_defaults();
$template->out(search_log($template));
