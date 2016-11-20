<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

if(user_loged())
	$_SESSION['forums']['viewed'][$forum_id] = $forum_data['forum_themecount'];

$template->set_file('FILE_forum', 'forum/theme.tpl');
$template->copy_block('BLOCK_middle', 'FILE_forum');

if($forum_id == 107488){
	$template->enable('BLOCK_forumdata_bazar');
}

if($action == 'add_theme')
{
	# Blacklisted
	if(user_blacklisted())
	{
		print "Blacklisted: $ip";
		return;
	}

	$error = false;
	$data = post('data');
	$forum->validate($data);

	$params = array(
		'get_votes'=>true,
		'get_comment_count'=>true,
		'l_id'=>$_SESSION['login']['l_id'],
		);
	$Logins = new Logins();
	$ldata = $Logins->load($params);

	$data['login_id'] = $_SESSION['login']['l_id'];
	$data['forum_userlogin'] = $_SESSION['login']['l_login'];
	$data['forum_useremail'] = $_SESSION['login']['l_email'];
	$data['forum_username'] = $_SESSION['login']['l_nick'];
	$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;

	if(!user_loged())
	{
		$error = true;
	}

	if(!$data['forum_name'])
	{
		$error = true;
		$template->enable('BLOCK_forumname_error');
	}

	if(!$data['forum_data'])
	{
		$error = true;
		$template->enable('BLOCK_forumdata_error');
	}

	# Tirgus
	if($forum_id == 107488){
		$entered_days = (time() - strtotime($ldata['l_entered'])) / (3600 * 24);
		if(($entered_days < 10) || ($ldata['votes_plus'] - $ldata['votes_minus'] < 10)){
			$error = true;
			$template->enable('BLOCK_forumdata_error_rating');
			$template->set_var('error_msg', 'Nepietiekams reitings. Jābūt vismaz 10 dienu vecam vai (plusi - mīnusi) vismaz 10', 'BLOCK_forumdata_error_rating');
		}
	}

	if(!$error)
	{
		$db->AutoCommit(false);
		$forum->setDb($db);
		if($id = $forum->add($forum_id, $data))
		{
			$newforum = new Forum;
			$new_data = $newforum->load(array(
				"forum_id"=>$id,
				));

			$res_id = $new_data['res_id'];
			$data['c_data'] = $data['forum_data'];
			$resDb = $db;
			if($c_id = include('module/comment/add.inc.php'))
			{
				$_SESSION['user']['username'] = $data['forum_username'];
				$_SESSION['user']['useremail'] = $data['forum_useremail'];
				$resDb->Commit();
				header("Location: $module_root/$id-".rawurlencode(urlize($data['forum_name'])));
				return;
			}
			$resDb->Commit();
		}
		$db->AutoCommit(true);
	}
	parse_form_data_array($data);
	$template->set_array($data, 'BLOCK_loggedin');
}


if(user_loged())
{
	$template->enable('BLOCK_loggedin');
	$template->set_var('forum_username', $_SESSION['login']['l_nick'], 'BLOCK_loggedin');
} else {
	$template->enable('BLOCK_notloggedin');
}

$forum_items = new Forum;
$forum_items->setItemsPerPage($fpp);
$forum_items->setPage($page_id);

if(
	isset($_SESSION['login']['l_forumsort_themes']) &&
	($_SESSION['login']['l_forumsort_themes'] == FORUM_SORT_LAST_COMMENT)
)
{
	$items = $forum_items->load(array(
		"forum_forumid"=>$forum_id,
		"order"=>"forum_lastcommentdate DESC",
		));

	if($items)
		$template->enable('BLOCK_info_sort_C');
} else {
	$items = $forum_items->load(array(
		"forum_forumid"=>$forum_id,
		));

	if($items)
		$template->enable('BLOCK_info_sort_T');
}

if($items)
{
	$template->enable('BLOCK_forum_themes');
} else {
	$template->enable('BLOCK_noforum');
}

foreach($items as $item)
{
	$template->{(Forum::hasNewComments($item) ? "enable" : "disable")}('BLOCK_comments_new');

	$item['forum_name_urlized'] = rawurlencode(urlize($item['forum_name']));

	$template->set_array($item, 'BLOCK_forum');
	$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
	$template->parse_block('BLOCK_forum', TMPL_APPEND);
}

$forum_count = $forum_items->getThemeCount($forum_id);
include('module/forum/pages.inc.php');

set_forum($template, $forum_id);

