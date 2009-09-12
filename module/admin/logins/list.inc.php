<?php

$l = $logins->load(array(
	'limit'=>100,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL
	));

if(count($l))
	$template->enable('BLOCK_logins_list');
else
	$template->enable('BLOCK_nologins');

$logins_count = 0;
foreach($l as $item) {
	++$logins_count;
	$template->set_var('logins_nr', $logins_count);
	$template->set_array($item);

	$template->set_var('l_color_class', 'box-normal');
	if($item['l_active'] != LOGIN_ACTIVE)
		$template->set_var('l_color_class', 'box-inactive');

	$template->parse_block('BLOCK_logins', TMPL_APPEND);
} // foreach logins
$template->set_var('logins_count', $logins_count);

