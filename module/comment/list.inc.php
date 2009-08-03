<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

if(user_loged())
{
	$template->enable('BLOCK_addcomment');
	$template->set_var('acd_username', $_SESSION['login']['l_nick']);
} else {
	$template->enable('BLOCK_notloggedin');
}

if($comments)
{
	$template->enable('BLOCK_comment');
} else {
	$template->enable('BLOCK_nocomment');
}

if(user_loged())
	$template->set_var('c_username', $_SESSION['login']['l_nick'], 'BLOCK_addcomment');

foreach($comments as $item)
{
	# balsošana
	if(user_loged() && $template->block_isset('BLOCK_comment_vote'))
	{
		$template->enable('BLOCK_comment_vote');
		if($item['c_votes'] > 0)
		{
			$template->set_var('comment_vote_class', 'plus', 'BLOCK_comment');
			$item['c_votes'] = '+'.$item['c_votes'];
		} elseif($item['c_votes'] < 0) {
			$template->set_var('comment_vote_class', 'minus', 'BLOCK_comment');
		} else {
			//$template->set_var('comment_vote_class', 'Comment-Vote', 'BLOCK_comment');
		}
	}

	if($hl)
	{
		hl($item['c_datacompiled'], $hl);
	}

	$item['c_username'] = parse_form_data($item['c_username']);
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
	elseif($item['c_userid'])
		$template->set_var('user_login_id', $item['c_userid']);

	if($item['c_userlogin'] || $item['c_userid'])
		$template->enable('BLOCK_profile_link');
	else
		$template->disable('BLOCK_profile_link');

	$template->parse_block('BLOCK_comment_list', TMPL_APPEND);
} // foreach


