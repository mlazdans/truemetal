<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// ----------------------------------------------------------------------------

/*
function set_forum_items(&$template, &$data, $forum_comments = false)
{
	global $forum, $page_id, $fpp, $pages_visible_to_sides, $hl, $forum_count,
		$sys_user_root, $sys_user_http_root,
		$user_pic_tw, $user_pic_th, $user_pic_w, $user_pic_h;

	$is_block_paged = $template->block_isset('BLOCK_is_pages');
	$is_block_avatar = $template->block_isset('BLOCK_forum_avatar');
	$is_block_profile = $template->block_isset('BLOCK_profile_link');
	$is_block_vote = $template->block_isset('BLOCK_forum_comment_vote');
	//$is_block_data = $template->block_isset('BLOCK_forum_data');
	$is_block_email = $template->block_isset('BLOCK_email');
	$is_block_username = $template->block_isset('BLOCK_username');
	$user_loged = user_loged();
	$enable_avatars = empty($_SESSION['login']['l_disable_avatars']);

	if($forum_count)
	{
		$template->enable('BLOCK_forum');
	} else {
		$template->enable('BLOCK_noforum');
	}

	$total_pages = ceil($forum_count / $fpp);
	$is_forum = is_object($forum);

	if($is_block_paged)
	{
		include('inc.forum_pages.php');
	}

	$c = 0;
	foreach($data as $item)
	{
		$c++;
		if($forum_comments && $is_forum)
		{
			$forum_childcount = $item['forum_childcount'];
			$template->set_var('forum_childcount', $forum_childcount, 'FILE_forum');

			// iekraaso komentaarus
			$template->disable('BLOCK_comments_new');
			//$template->enable('BLOCK_comments_old');
			if($forum_childcount)
			{
				$old_forum_childcount =
				isset($_SESSION['forums']['viewed'][$item['forum_id']]) ?
				$_SESSION['forums']['viewed'][$item['forum_id']] :
				0;

				$template->disable('BLOCK_comments_new');
				//$template->disable('BLOCK_comments_old');

				if($forum_childcount > $old_forum_childcount)
					$template->enable('BLOCK_comments_new');
				//else
					//$template->enable('BLOCK_comments_old');
			} // session forum viewed
		} else { // if forum_comments
			if($hl)
			{
				hl($item['forum_datacompiled'], $hl);
			}
		}

		if($is_block_profile)
		{
			if($item['forum_userlogin'])
				$template->set_var('user_login_id', $item['forum_userlogin'], 'FILE_forum');
			elseif($item['forum_userid'])
				$template->set_var('user_login_id', $item['forum_userid'], 'FILE_forum');

			if($item['forum_userlogin'] || $item['forum_userid'])
				$template->enable('BLOCK_profile_link');
			else
				$template->disable('BLOCK_profile_link');
		}

		# balsošana
		if($is_block_vote && $user_loged)
		{
			$template->enable('BLOCK_forum_comment_vote');
			if($item['forum_votes'] > 0)
			{
				//$template->enable('BLOCK_forum_comment_vote_value');
				$template->set_var('comment_vote_class', 'Comment-Vote-plus', 'FILE_forum');
				$item['forum_votes'] = '+'.$item['forum_votes'];
			} elseif($item['forum_votes'] < 0) {
				//$template->enable('BLOCK_forum_comment_vote_value');
				$template->set_var('comment_vote_class', 'Comment-Vote-minus', 'FILE_forum');
			} else {
				//$template->enable('BLOCK_forum_comment_vote_value');
				$template->set_var('comment_vote_class', 'Comment-Vote', 'FILE_forum');
			}
		}

		//if($item['forum_datacompiled'] && $is_block_data)
		//	$template->enable('BLOCK_forum_data');

		if($is_block_email)
		{
			if($item['forum_useremail'])
			{
				$template->enable('BLOCK_email');
				if($is_block_username)
					$template->disable('BLOCK_username');
				$template->set_var('forum_useremail', $item['forum_useremail'], 'FILE_forum');
			} else {
				if($is_block_username)
					$template->enable('BLOCK_username');
				$template->disable('BLOCK_email');
			}
		}

		# Avatāri
		if($is_block_avatar)
		{
			if($user_loged && $enable_avatars)
			{
				$at_id = (int)$item['forum_userid'];

				$pic_path = "$sys_user_root/pic/thumb/$at_id.jpg";
				$pic_http_path = "$sys_user_http_root/pic/thumb/$at_id.jpg";

				if(file_exists($pic_path))
				{
					$template->enable('BLOCK_forum_avatar');
					$template->set_var('avatar_path', $pic_http_path, 'BLOCK_forum_avatar');
					$template->set_var('avatar_w', $user_pic_tw / 2, 'BLOCK_forum_avatar');
					$template->set_var('avatar_h', $user_pic_th, 'BLOCK_forum_avatar');
					$template->set_var('pic_w', $user_pic_w, 'BLOCK_forum_avatar');
					$template->set_var('pic_h', $user_pic_h, 'BLOCK_forum_avatar');
				} else {
					$template->disable('BLOCK_forum_avatar');
					//$template->set_var('pic_path', $sys_http_root.'/img/1x1.gif');
				}
			} else {
				$template->disable('BLOCK_forum_avatar');
			}
		}

		$template->set_array($item, 'BLOCK_forum');
		$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
		$template->parse_block('BLOCK_forum', TMPL_APPEND);
	}
}
*/

// ----------------------------------------------------------------------------
require_once('lib/MainModule.php');
require_once('lib/Forum.php');

$hl = urldecode(get("hl"));
$action = post('action');

$fpp = 20;
$page_id = 1;
$pages_visible_to_sides = 8;
$forum_id = (int)array_shift($sys_parameters);
$page = array_shift($sys_parameters);
if($page == 'page')
	$page_id = (int)array_shift($sys_parameters);

if(!$page_id)
	$page_id = 1;

$forum = new Forum();
if($forum_id == 0)
{
	$forum_data = $forum->load(array(
		"forum_forumid"=>0,
		"order"=>"forum_id ASC",
		));
	//$forum_data = $forum->load($forum_id, 0, FORUM_ACTIVE, "forum_id ASC");
} else {
	//$forum_data = $forum->load($forum_id);
	$forum_data = $forum->load(array(
		"forum_id"=>$forum_id,
		));
}

$template = new MainModule($sys_template_root, $sys_module_id);

// ---------------------------------------------
// ja izveeleets forums, paraadam teemu sarakstu
$forum_title = 'Diskusijas';

if($forum_id)
{
	if(!$forum_data)
	{
		header("Location: $module_root/");
		return;
	}

	$forum_title .= ' - '.$forum_data['forum_name'].($hl ? sprintf(", meklēšana: %s", $hl) : "");
	if($page_id > 1)
		$forum_title .= " - $page_id. lapa";

	$template->set_var('current_forum_id', $forum_id);
	# Subtēma TODO: jānotestē
	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS)
	{
		include("forum/theme.inc.php");
	} else {
		include("forum/det.inc.php");
	}
} else {
	include("forum/root.inc.php");
}

/*
if($forum_id)
{
	return;
	//$template->enable('BLOCK_addressbar');
	$_SESSION['forums']['viewed'][$forum_id] = $forum_data['forum_comment_count'];

	// ja useris saglabaats
	if(user_loged())
	{
		$template->enable('BLOCK_loggedin');
		$template->set_var('forumd_username', $_SESSION['login']['l_nick'], 'FILE_forum');
	} else {
		$template->enable('BLOCK_notloggedin');
	}

	set_forum($template, $forum_id);

	// apaksitemi
	//$item_data = $forum->load(0, $forum_id);

	$forum_items = new Forum;
	if($forum_data['forum_allowchilds'] == FORUM_PROHIBITCHILDS)
	{
		if(
			isset($_SESSION['login']['l_forumsort_msg']) &&
			($_SESSION['login']['l_forumsort_msg'] == FORUM_SORT_DESC)
		)
		{
			$item_data = $forum_items->load(0, $forum_id, FORUM_ACTIVE, 'forum_entered DESC');
			$template->enable('BLOCK_info_sort_D');
		} else {
			$item_data = $forum_items->load(0, $forum_id, FORUM_ACTIVE, 'forum_entered');
			$template->enable('BLOCK_info_sort_A');
		}
		$forum_count = count($item_data);
		set_forum_items($template, $item_data);
	} else {
		$forum_items->setItemsPerPage($fpp);
		$forum_items->setPage($page_id);
		if(
			isset($_SESSION['login']['l_forumsort_themes']) &&
			($_SESSION['login']['l_forumsort_themes'] == FORUM_SORT_LAST_COMMENT)
		)
		{
			//# !WRONG! NOTE: labāka performance, ja sortē no bāzes tabulas, jo ID tāpat pieaug
			//$item_data = $forum_items->load(0, $forum_id, FORUM_ACTIVE, "f.forum_id DESC");
			$item_data = $forum_items->load(0, $forum_id, FORUM_ACTIVE, "fm.forum_lastcommentdate DESC");
			$template->enable('BLOCK_info_sort_C');
		} else {
			$item_data = $forum_items->load(0, $forum_id);
			$template->enable('BLOCK_info_sort_T');
		}

		if(!$item_data)
		{
			//header("Location: $module_root/$forum_id/");
			//return;
		}

		$forum_count = $forum_items->getCount(0, $forum_id);
		set_forum_items($template, $item_data, FORUM_COMMENTS);
	}

	include('inc.forum_actions.php');

// --------------------------------------------------
// ja nekas nav izveeleets, paraadaam forumu sarakstu
} else {
} // forum_id
*/

$template->set_title($forum_title);

$template->set_right();
$template->set_recent_forum();
$template->set_login();
$template->set_online();
$template->set_search();

$template->out();

