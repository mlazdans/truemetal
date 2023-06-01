<?php

use dqdp\DBA\Types\None;
use dqdp\Template;

function admin_logins_list(AdminModule $template): ?Template
{
	$q = postget('q');
	$sort = postget('sort');
	$action = postget('action');

	$desc = true;
	if(substr($sort, -4) == '_asc')
	{
		$desc = false;
		$sort = substr($sort, 0, -4);
	}

	$sortables = ["votes", "comment_count", "l_entered", "l_lastaccess"];

	$template->set_title('Admin :: logini :: saraksts');

	$T = $template->add_file("admin/logins/list.tpl");

	$sort_f = $sort;

	if($sort == 'votes'){
		$sort_f = "votes_plus - votes_minus";
	}

	$F = (new LoginsFilter(
		l_active:false,
		l_accepted:false,
	))->rows(200)->orderBy("l_entered DESC");

	if($sort_f){
		$F->orderBy("$sort_f".($desc ? " DESC" : ""));
	}

	if($action == 'search')
	{
		if($q){
			$F->q = $q;
		}

		if(postget('l_active_y'))
		{
			$F->l_active = 1;
			$T->set_var('ls_l_active_y_checked', 'checked="checked"');
		} elseif(postget('l_active_n')) {
			$F->l_active = 0;
			$T->set_var('ls_l_active_n_checked', 'checked="checked"');
		}

		if(postget('l_accepted_y'))
		{
			$F->l_accepted = 1;
			$T->set_var('ls_l_accepted_y_checked', 'checked="checked"');
		} elseif(postget('l_accepted_n')) {
			$F->l_accepted = 0;
			$T->set_var('ls_l_accepted_n_checked', 'checked="checked"');
		}

		if(postget('l_notloggedever'))
		{
			$F->l_lastaccess = new None;
			$T->set_var('ls_l_notloggedever_checked', 'checked="checked"');
		} else {
			$T->set_var('ls_l_notloggedever_checked', '');
		}

		$T->set_var('q', specialchars($q), "BLOCK_logins_search_from");
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
		$T->set_var("q_sort_{$sa}", specialchars($qs), "BLOCK_logins_list");
	}

	$logins = Logins::load($F);

	if(count($logins))
		$T->enable('BLOCK_logins_list');
	else
		$T->enable('BLOCK_nologins');

	$logins_count = 0;
	foreach($logins as $item)
	{
		$T->set_array(specialchars($item));

		// if(is_null($item->l_lastaccess)){
		// 	$T->set_var('l_lastaccess', "<i>-nekad-</i>");
		// }
		$T->set_var('votes', $item->votes_plus - $item->votes_minus);
		if($item->votes_plus + $item->votes_minus != 0){
			$T->set_var('votes_perc', number_format(($item->votes_plus / ($item->votes_plus + $item->votes_minus)) * 100, 2, '.', ''));
		} else {
			$T->set_var('votes_perc', 0);
		}

		$T->set_var('logins_nr', ++$logins_count);

		$T->set_var('l_color_class', 'box-normal');
		if(!$item->l_active)
			$T->set_var('l_color_class', 'box-inactive');

		$T->parse_block('BLOCK_logins', TMPL_APPEND);
	}

	$T->set_var('logins_count', $logins_count);

	return $T;
}

function admin_logins_edit(AdminModule $template, int $l_id): ?Template
{
	global $sys_user_root;

	$action = postget('action');

	# Comment actions
	if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
	{
		# TODO: move to function
		if(include('module/admin/comment/action.inc.php')){
			header("Location: ".($l_id ? "/admin/logins/$l_id/" : "/admin/logins/"));
		}
		return null;
	}

	$template->set_title('Admin :: logini :: rediģēt');

	$T = $template->add_file("admin/logins/edit.tpl");

	$login = Logins::load_by_id($l_id, true);

	// $all_ips = $login['all_ips']??"";

	$T->set_array($login, 'BLOCK_login_edit');

	$YN = array(
		'l_active',
		'l_accepted',
		'l_emailvisible',
		'l_logedin',
	);

	foreach($YN as $k)
	{
		$v = sprintf("%s_%s_sel", $k, $login->{$k} ? 'Y' : 'N');
		$T->set_var($v, ' selected="selected"', 'BLOCK_login_edit');
	}
	// $T->set_var('all_ips_view', str_replace(",", ", ", $all_ips), 'BLOCK_login_edit');

	# User comments
	$C = new_template("admin/comment/list.tpl");

	$CF = (new ResCommentFilter(
		login_id: $l_id,
		res_visible:false,
	))->rows(500)->orderBy("res_entered DESC");

	$comments = (new ViewResCommentEntity)->getAll($CF);

	// $comments = (new ResComment)->get([
	// 	'login_id'=>$l_id,
	// 	'c_visible'=>Res::STATE_ALL,
	// 	'order'=>'c_entered DESC',
	// 	'limit'=>500,
	// ]);

	# Šmurguļi, kas nāk no vairākā IP un reklamē :E (piemēram, HeavenGrey)
	// $alsoUsers = Logins::collectUsersByIP(explode(",", $all_ips), $l_id);
	// if($alsoUsers)
	// {
	// 	$T->enable('BLOCK_logins_also');
	// 	foreach($alsoUsers as $item)
	// 	{
	// 		$T->set_array($item, 'BLOCK_logins_also_list');
	// 		$T->set_var('l_color_class', 'box-normal', 'BLOCK_logins_also_list');
	// 		if($item['l_active'] != Res::STATE_ACTIVE)
	// 			$T->set_var('l_color_class', 'box-inactive', 'BLOCK_logins_also_list');
	// 		$T->parse_block('BLOCK_logins_also_list', TMPL_APPEND);
	// 	}
	// }

	# Bildes
	$pic_suffixes = array();

	# Main pic
	if(file_exists("$sys_user_root/pic/thumb/$l_id.jpg")){
		$pic_suffixes = array('');
	}

	# Pic history
	$files = scandir("$sys_user_root/pic/thumb", 1);

	foreach($files as $k=>$v){
		if(preg_match("/^$l_id-(\d+).jpg\$/", $v, $m)){
			$pic_suffixes[] = $m[1];
		}
	}

	if($pic_suffixes)
	{
		rsort($pic_suffixes);
		$T->enable('BLOCK_logins_pics');
		foreach($pic_suffixes as $suffix){
			$T->set_var('l_pic_suffix', $suffix, 'BLOCK_logins_pics_item');
			$T->parse_block('BLOCK_logins_pics_item', TMPL_APPEND);
		}
	}

	admin_comment_list($C, $comments);

	$T->set_block_string($C->parse(), 'BLOCK_login_comments');

	return $T;
}
