<?php

$template = new AdminModule($sys_template_root.'/admin', "logins/list");
$template->set_title('Admin :: logini :: saraksts');

$logins = $logins->load(array(
	'limit'=>100,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL,
	));

if(count($logins))
	$template->enable('BLOCK_logins_list');
else
	$template->enable('BLOCK_nologins');

$logins_count = 0;
foreach($logins as $item)
{
	$template->set_array($item, 'BLOCK_logins');
	$template->set_var('logins_nr', ++$logins_count, 'BLOCK_logins');

	$template->set_var('l_color_class', 'box-normal', 'BLOCK_logins');
	if($item['l_active'] != LOGIN_ACTIVE)
		$template->set_var('l_color_class', 'box-inactive', 'BLOCK_logins');

	$template->parse_block('BLOCK_logins', TMPL_APPEND);
}

$template->set_var('logins_count', $logins_count, 'BLOCK_logins_list');

$template->out();

