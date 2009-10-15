<?php

$q = post('q');

$template = new AdminModule($sys_template_root.'/admin', "logins/list");
$template->set_title('Admin :: logini :: saraksts');

$params = array(
	'limit'=>100,
	'get_votes'=>true,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL,
	);

if($action == 'search')
{
	if($q)
		$params['q'] = $q;

	if(post('l_active_y'))
	{
		$params['l_active'] = LOGIN_ACTIVE;
		$template->set_var('ls_l_active_y_checked', 'checked="checked"');
	} elseif(post('l_active_n')) {
		$params['l_active'] = LOGIN_INACTIVE;
		$template->set_var('ls_l_active_n_checked', 'checked="checked"');
	}

	if(post('l_accepted_y'))
	{
		$params['l_accepted'] = LOGIN_ACCEPTED;
		$template->set_var('ls_l_accepted_y_checked', 'checked="checked"');
	} elseif(post('l_accepted_n')) {
		$params['l_accepted'] = LOGIN_NOTACCEPTED;
		$template->set_var('ls_l_accepted_n_checked', 'checked="checked"');
	}

	$template->set_var('q', parse_form_data($q), "BLOCK_logins_search_from");
}

$logins = $logins->load($params);

if(count($logins))
	$template->enable('BLOCK_logins_list');
else
	$template->enable('BLOCK_nologins');

$logins_count = 0;
foreach($logins as $item)
{
	$item['votes_plus'] = (int)$item['votes_plus'];
	$item['votes_minus'] = abs($item['votes_minus']);
	$item['votes'] = $item['votes_plus'] - $item['votes_minus'];

	$template->set_array($item, 'BLOCK_logins');
	$template->set_var('logins_nr', ++$logins_count, 'BLOCK_logins');

	$template->set_var('l_color_class', 'box-normal', 'BLOCK_logins');
	if($item['l_active'] != LOGIN_ACTIVE)
		$template->set_var('l_color_class', 'box-inactive', 'BLOCK_logins');

	$template->parse_block('BLOCK_logins', TMPL_APPEND);
}

$template->set_var('logins_count', $logins_count, 'BLOCK_logins_list');

$template->out();

