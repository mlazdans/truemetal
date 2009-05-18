<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// ----------------------------------------------------------------------------
$field_b = '';
if(!empty($_SESSION['login']['l_disable_bobi']))
{
	$field_b = '_b';
}

function set_forum_items(&$template, &$data, $forum_comments = false)
{
	global $forum, $page_id, $fpp, $pages_visible_to_sides, $hl, $forum_count;

	if($forum_count)
	{
		$template->enable('BLOCK_forum');
	} else {
		$template->enable('BLOCK_noforum');
	}

	$total_pages = ceil($forum_count / $fpp);
	$is_forum = is_object($forum);

	$is_paged = $template->block_isset('BLOCK_is_pages');
	if($is_paged)
	{
		include('inc.forum_pages.php');
	}

	$c = 0;
	foreach($data as $item)
	{
		$c++;
		/*
		if($is_paged)
		{
			if($c <= ($page_id - 1) * $fpp)
			{
				continue;
			}

			if($c > $page_id * $fpp)
			{
				break;
			}
		}*/

		if($forum_comments && $is_forum)
		{
			$forum_childcount = $item['forum_childcount'];
			$template->set_var('forum_childcount', $forum_childcount, 'FILE_forum');

			// iekraaso komentaarus
			$template->disable('BLOCK_comments_new');
			$template->enable('BLOCK_comments_old');
			if($forum_childcount)
			{
				$old_forum_childcount =
				isset($_SESSION['forums']['viewed'][$item['forum_id']]) ?
				$_SESSION['forums']['viewed'][$item['forum_id']] :
				0;

				$template->disable('BLOCK_comments_new');
				$template->disable('BLOCK_comments_old');

				if($forum_childcount > $old_forum_childcount)
					$template->enable('BLOCK_comments_new');
				else
					$template->enable('BLOCK_comments_old');
			} // session forum viewed
		} else { // if forum_comments
			if($hl)
			{
				hl($item['forum_datacompiled'], $hl);
			}
		}

		if($template->block_isset('BLOCK_profile_link'))
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
		if(user_loged() && $template->block_isset('BLOCK_forum_comment_vote'))
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

		if($item['forum_datacompiled'] && $template->block_isset('BLOCK_forum_data'))
		{
			$template->enable('BLOCK_forum_data');
		}

		if($template->block_isset('BLOCK_email'))
		{
			if($item['forum_useremail'])
			{
				$template->enable('BLOCK_email');
				if($template->block_isset('BLOCK_username'))
					$template->disable('BLOCK_username');
				$template->set_var('forum_useremail', $item['forum_useremail'], 'FILE_forum');
			} else {
				if($template->block_isset('BLOCK_username'))
					$template->enable('BLOCK_username');
				$template->disable('BLOCK_email');
			}
		}

		$template->set_array($item, 'BLOCK_forum');
		$template->set_var('forum_date', proc_date($item['forum_entered']), 'BLOCK_forum');
		$template->parse_block('BLOCK_forum', TMPL_APPEND);
	}
}

// ----------------------------------------------------------------------------
require_once('../classes/class.MainModule.php');
require_once('../classes/class.Forum.php');

$hl = urldecode(join('', $sys_parameters));
preg_match('/hl=([^&]*)/i', $hl, $m);
$hl = isset($m[1]) ? $m[1] : '';

$action = isset($_POST['action']) ? $_POST['action'] : '';

$fpp = 20;
$page_id = 1;
$forum = new Forum();
$forum_id = (integer)array_shift($sys_parameters);
$pages_visible_to_sides = 5;

$page = array_shift($sys_parameters);
if($page == 'page')
{
	$page_id = (integer)array_shift($sys_parameters);
}

if(!$page_id)
{
	$page_id = 1;
}

if($forum_id == 0)
{
	$forum_data = $forum->load($forum_id, 0, FORUM_ACTIVE, "forum_id ASC");
} else {
	$forum_data = $forum->load($forum_id);
}
//printr($forum_data);

//$forum_path = join('/', $forums);
//$forum_path .= $forum_path ? '/' : '';

//$_pointer = &$sys_modules[$sys_module_id];

$template = new MainModule($sys_template_root, $sys_module_id);
//$template->set_var('forum_path', $forum_path);
//$template->enable('BLOCK_addressbar');

/*
// jau kaukas nau
if($forum_id)
{
	if(!isset($forum_data['forum_id']) || (isset($forum_data['forum_id']) && $forum_data['forum_id'] != $forum_id))
	{
//		header("Location: $module_root/");
//		exit;
	}
}
*/

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

	$forum_title .= ' - '.$forum_data['forum_name'];

	$template->set_var('current_forum_id', $forum_id, 'BLOCK_middle');
	if($forum_data['forum_allowchilds'] == FORUM_ALLOWCHILDS)
	{
		$template->set_file('FILE_forum', 'tmpl.forum_theme.php');
		$template->copy_block('BLOCK_middle', 'FILE_forum');
		$template->enable('BLOCK_forum_form');
	} else {
		//$forum_data = $forum->load($forum_id);
		$template->set_file('FILE_forum', 'tmpl.forum_det.php');
		$template->copy_block('BLOCK_middle', 'FILE_forum');
	}
	$template->enable('BLOCK_addressbar');
	$forum_childcount = $forum_data['forum_childcount'];
	$_SESSION['forums']['viewed'][$forum_id] = $forum_childcount;

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
			//# NOTE: labāka performance, ja sortē no bāzes tabulas, jo ID tāpat pieaug
			//$item_data = $forum_items->load(0, $forum_id, FORUM_ACTIVE, "f.forum_id DESC");
			$item_data = $forum_items->load(0, $forum_id, FORUM_ACTIVE, "fm.forum_lastcommentdate$field_b DESC");
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
	$forum_count = count($forum_data);
	$template->set_file('FILE_forum', 'tmpl.forum.php');
	$template->copy_block('BLOCK_middle', 'FILE_forum');

	// forumu saraksts
	set_forum_items($template, $forum_data, FORUM_COMMENTS);

} // forum_id

$template->set_title($forum_title);
//$template->set_var('login_id', $_SESSION['login']['l_id']);
$template->set_right();
$template->set_recent_forum();
$template->set_login();
$template->set_poll();
$template->set_search();
$template->set_online();
$template->set_calendar();
//print_r($template);
//die;
$template->out();

