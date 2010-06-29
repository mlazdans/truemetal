<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/CommentDisabled.php');

if(user_loged())
{
	$template->enable('BLOCK_addcomment');
	$template->set_var('acd_username', $_SESSION['login']['l_nick']);
	$template->set_var('c_username', $_SESSION['login']['l_nick'], 'BLOCK_addcomment');
	$disabled_users = CommentDisabled::get($_SESSION['login']['l_id']);
} else {
	$template->enable('BLOCK_notloggedin');
	$disabled_users = array();
}

if($comments)
{
	$template->enable('BLOCK_comment');
	$template->set_descr((empty($hl) ? "" : sprintf("(Meklēšana: %s) ", trim($hl))).$comments[0]['c_datacompiled']);
} else {
	$template->enable('BLOCK_nocomment');
}

foreach($comments as $item)
{
	$item['res_votes'] = (int)$item['res_votes'];
	# balsošana
	if(user_loged() && $template->block_isset('BLOCK_comment_vote'))
	{
		$template->enable('BLOCK_comment_vote');
		if($item['res_votes'] > 0)
		{
			$template->set_var('comment_vote_class', 'plus', 'BLOCK_comment');
			$item['res_votes'] = '+'.$item['res_votes'];
		} elseif($item['res_votes'] < 0) {
			$template->set_var('comment_vote_class', 'minus', 'BLOCK_comment');
		} else {
			$template->set_var('comment_vote_class', '', 'BLOCK_comment');
		}
	}

	if($hl)
	{
		hl($item['c_datacompiled'], $hl);
	}

	# Old id
	if($item['cm_old_id'])
	{
		$template->enable('BLOCK_comment_old_id');
	} else {
		$template->disable('BLOCK_comment_old_id');
	}

	$item['c_username'] = parse_form_data($item['c_username']);
	if(empty($disabled_users[$item['login_id']]))
	{
		$template->set_var('c_disabled_user_class', '');
	} else {
		$template->set_var('c_disabled_user_class', ' disabled');
		$item['c_datacompiled'] = '-neredzams komentārs-';
	}

	$template->set_array($item, 'BLOCK_comment');
	$template->set_var('c_date', proc_date($item['c_entered']));

	/*
	if($item['c_useremail'])
		$template->enable('BLOCK_email');
	else
		$template->disable('BLOCK_email');
	*/

	if($item['c_userlogin'])
		$template->set_var('user_login_id', $item['c_userlogin']);
	elseif($item['login_id'])
		$template->set_var('user_login_id', $item['login_id']);

	if(user_loged() && ($item['c_userlogin'] || $item['login_id']))
		$template->enable('BLOCK_profile_link');
	else
		$template->disable('BLOCK_profile_link');

	$template->parse_block('BLOCK_comment_list', TMPL_APPEND);
} // foreach


