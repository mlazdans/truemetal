<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$q = postget('q');
$sort = postget('sort');
$desc = true;
if(substr($sort, -4) == '_asc')
{
	$desc = false;
	$sort = substr($sort, 0, -4);
}

$sortables = array(
	"votes",
	"comment_count",
	"l_entered"
	);

$template = new AdminModule("logins/list");
$template->set_title('Admin :: logini :: saraksts');

$sortr = $sort;

if($sort == 'votes'){
	$sortr = "votes_plus - votes_minus";
}

$params = array(
	'limit'=>200,
	'get_votes'=>true,
	'get_comment_count'=>true,
	'l_active'=>Res::STATE_ALL,
	'l_accepted'=>Res::STATE_ALL,
	'order'=>($sortr ? $sortr.($desc ? " DESC" : " ASC") : ""),
	);

if($action == 'search')
{
	if($q)
		$params['q'] = $q;

	if(postget('l_active_y'))
	{
		$params['l_active'] = Res::STATE_ACTIVE;
		$template->set_var('ls_l_active_y_checked', 'checked="checked"');
	} elseif(postget('l_active_n')) {
		$params['l_active'] = Res::STATE_INACTIVE;
		$template->set_var('ls_l_active_n_checked', 'checked="checked"');
	}

	if(postget('l_accepted_y'))
	{
		$params['l_accepted'] = Logins::ACCEPTED;
		$template->set_var('ls_l_accepted_y_checked', 'checked="checked"');
	} elseif(postget('l_accepted_n')) {
		$params['l_accepted'] = Logins::NOT_ACCEPTED;
		$template->set_var('ls_l_accepted_n_checked', 'checked="checked"');
	}

	if(postget('l_notloggedever'))
	{
		$params['l_lastaccess'] = '0000-00-00 00:00:00';
		$params['comment_count_equal'] = 0;
		$template->set_var('ls_l_notloggedever_checked', 'checked="checked"');
	} else {
		$template->set_var('ls_l_notloggedever_checked', '');
	}

	$template->set_var('q', parse_form_data($q), "BLOCK_logins_search_from");
}

foreach($sortables as $sa)
{
	if($sort && $desc)
	{
		$sort_new = $sa.'_asc';
	} else {
		$sort_new = $sa;
	}
	$qs = queryl("-sort=&sort=$sort_new");
	$template->set_var("q_sort_{$sa}", parse_form_data($qs), "BLOCK_logins_list");
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
	$item['votes_minus'] = (int)$item['votes_minus'];
	$item['votes'] = $item['votes_plus'] - $item['votes_minus'];
	if($item['votes_plus'] + $item['votes_minus'] != 0){
		$item['votes_perc'] = number_format(($item['votes_plus'] / ($item['votes_plus'] + $item['votes_minus'])) * 100, 2, '.', '');
	} else {
		$item['votes_perc'] = 0;
	}

	$template->set_array($item, 'BLOCK_logins');
	$template->set_var('logins_nr', ++$logins_count, 'BLOCK_logins');

	$template->set_var('l_color_class', 'box-normal', 'BLOCK_logins');
	if($item['l_active'] != Res::STATE_ACTIVE)
		$template->set_var('l_color_class', 'box-inactive', 'BLOCK_logins');

	$template->parse_block('BLOCK_logins', TMPL_APPEND);
}

$template->set_var('logins_count', $logins_count, 'BLOCK_logins_list');

$template->out();

