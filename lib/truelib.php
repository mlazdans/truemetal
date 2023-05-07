<?php declare(strict_types = 1);

function forum_themes(
	int $forum_id,
	array $forum_data,
	MainModule $template,
	string $action,
	int $fpp,
	int $page_id,
	int $pages_visible_to_sides,
): ?Template
{
	global $db, $ip;

	if(user_loged())
	{
		Forum::markThemeCount($forum_data);
	}

	$T = $template->add_file('forum/theme.tpl');

	$T->set_var('current_forum_id', $forum_id);
	$T->set_var('current_forum_name_urlized', rawurlencode(urlize($forum_data['forum_name'])));

	if($forum_id == 107488){
		$T->enable('BLOCK_forumdata_bazar');
	}

	$forum = new Forum;

	if($action == 'add_theme')
	{
		# Blacklisted
		if(user_blacklisted())
		{
			print "Blacklisted: $ip";
			return null;
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
		$data['forum_allowchilds'] = Forum::PROHIBIT_CHILDS;

		if(!user_loged())
		{
			$error = true;
		}

		if(!$data['forum_name'])
		{
			$error = true;
			$T->enable('BLOCK_forumname_error');
		}

		if(!$data['forum_data'])
		{
			$error = true;
			$T->enable('BLOCK_forumdata_error');
		}

		# Tirgus
		if($forum_id == 107488){
			$entered_days = (time() - strtotime($ldata['l_entered'])) / (3600 * 24);
			if(($entered_days < 10) || ($ldata['votes_plus'] - $ldata['votes_minus'] < 10)){
				$error = true;
				$T->enable('BLOCK_forumdata_error_rating');
				$T->set_var('error_msg', 'Nepietiekams reitings. Jābūt vismaz 10 dienu vecam vai (plusi - mīnusi) vismaz 10', 'BLOCK_forumdata_error_rating');
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
					return null;
				}
				$resDb->Commit();
			}
			$db->AutoCommit(true);
		}
		parse_form_data_array($data);
		$T->set_array($data, 'BLOCK_loggedin');
	}

	if(user_loged())
	{
		$T->enable('BLOCK_loggedin');
		$T->set_var('forum_username', $_SESSION['login']['l_nick'], 'BLOCK_loggedin');
	} else {
		$T->enable('BLOCK_notloggedin');
	}

	$forum_items = new Forum;
	$forum_items->setItemsPerPage($fpp);
	$forum_items->setPage($page_id);

	if(
		isset($_SESSION['login']['l_forumsort_themes']) &&
		($_SESSION['login']['l_forumsort_themes'] == Forum::SORT_LASTCOMMENT)
	)
	{
		$items = $forum_items->load(array(
			"forum_forumid"=>$forum_id,
			"order"=>"res_comment_lastdate DESC",
			));

		if($items)
			$T->enable('BLOCK_info_sort_C');
	} else {
		$items = $forum_items->load(array(
			"forum_forumid"=>$forum_id,
			));

		if($items)
			$T->enable('BLOCK_info_sort_T');
	}

	if($items)
	{
		$T->enable('BLOCK_forum_themes');
	} else {
		$T->enable('BLOCK_noforum');
	}

	$BLOCK_forum = $T->get_block('BLOCK_forum');
	foreach($items as $item)
	{
		$BLOCK_forum->enable_if(Forum::hasNewComments($item), 'BLOCK_comments_new');

		$BLOCK_forum->set_array($item);
		$BLOCK_forum->set_var('forum_name_urlized', rawurlencode(urlize($item['forum_name'])));
		$BLOCK_forum->set_var('forum_date', proc_date($item['forum_entered']));
		$BLOCK_forum->parse(TMPL_APPEND);
	}

	$forum_count = (int)$forum_data['forum_themecount'];
	forum_pages($page_id, $forum_count, $fpp, $pages_visible_to_sides, $T);

	$forum->set_forum_path($T, $forum_id);

	return $T;
}

function forum_pages(
	int $page_id,
	int $forum_count,
	int $fpp,
	int $pages_visible_to_sides,
	Template $template,
)
{
	$total_pages = ceil($forum_count / $fpp);

	# uzstaadam pages
	if($total_pages > 1)
	{
		$_pvs = $pages_visible_to_sides;
		$sep_count = 0;
		$visible_pages = $_pvs + (
			$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : $_pvs - ($total_pages - $page_id))
			: $_pvs - $page_id + 1
		);

		$side_sep = 1 + (
			$page_id > $_pvs ? ($total_pages - $page_id > $_pvs ? 0 : 1)
			: 1
		);

		$template->enable('BLOCK_is_pages');
		for($p = 1; $p <= $total_pages; $p++)
		{
			$p_id = ($total_pages > 10) && ($p < 10) ? "0$p" : $p;
			$template->set_var('p_id', $p_id);
			$template->set_var('page_id', $p);

			# atziimee, tekoshu page
			if($p == $page_id)
			{
				$template->set_var('page_style', ' style="color: #00AC00;"');
			} else {
				$template->set_var('page_style', '');
			}

			# skippo pa nevajadziigaas pages
			if(abs($p - $page_id) > $visible_pages)
			{
				$sep_count++;
				$template->set_var('page_seperator', (abs($p - $page_id) > $visible_pages) && (abs($p - $page_id) - $visible_pages <= $side_sep) ? '[..]' : '');
				$template->disable('BLOCK_page_switcher');
			} else {
				$template->enable('BLOCK_page_switcher');
				$template->set_var('page_seperator', '');
			}

			$template->parse_block('BLOCK_page', TMPL_APPEND);

		}
	}

	# prev
	$template->set_var('prev_page_id', ($page_id > 1) ? $page_id - 1 : $page_id);

	# next
	$template->set_var('next_page_id', ($page_id < $total_pages) ? $page_id + 1 : $page_id);
}

function forum_det(
	int $forum_id,
	array $forum_data,
	MainModule $template,
	string $action,
	string $hl,
): ?Template
{
	global $db;

	$forum = new Forum;

	$T = $template->add_file('forum/det.tpl');

	# TODO: Vajag uztaisīt:
	# 1) lai rāda foruma datus
	# 2) uztaisīt balsošanu par articles un forum
	# 3) pārkopēt foruma pirmā komenta votes uz foruma votēm
	# 4) izvākt pirmo foruma komentu

	Res::markCommentCount($forum_data);

	if(($forum_data['forum_closed'] == Forum::OPEN) && ($action == 'add_comment') && user_loged())
	{
		$res_id = $forum_data['res_id'];
		$data = post('data');
		$resDb = $db;
		if($c_id = include('module/comment/add.inc.php'))
		{
			$resDb->Commit();
			header("Location: ".Forum::Route($forum_data, $c_id));
			return null;
		}
	}

	# Attendees
	if(user_loged() && ($forum_data['type_id'] == Res::TYPE_EVENT))
	{
		$attended = false;
		$T->enable('BLOCK_attend');
		$T->set_var('res_id', $forum_data['res_id']);
		if($data = get_attendees((int)$forum_data['res_id']))
		{
			$T->enable('BLOCK_attend_list');
			$c = count($data);
			foreach($data as $k=>$item){
				if($item['a_attended'] && ($_SESSION['login']['l_id'] == $item['l_id'])){
					$attended = true;
				}
				$l_nick = $item['l_nick'];
				if(!$item['a_attended']){
					$l_nick = "<strike>$l_nick</strike>";
				}
				$l_nick .= ($k+1 < $c ? ', ' : '');
				$T->set_array($item, 'BLOCK_attend_list');
				$T->set_var('l_nick_', $l_nick, 'BLOCK_attend_list');
				$T->parse_block('BLOCK_attend_list', TMPL_APPEND);
			}
		}

		$ts = strtotime(date('d.m.Y', strtotime($forum_data['event_startdate']))) + 24 * 3600;
		if(time() < $ts){
			$T->enable('BLOCK_attend_'.($attended ? 'off' : 'on'));
		}
	}

	# Comments
	$params = array(
		'res_id'=>$forum_data['res_id'],
		);
	$params['order'] =
		isset($_SESSION['login']['l_forumsort_msg']) &&
		($_SESSION['login']['l_forumsort_msg'] == Forum::SORT_DESC)
		? "c_entered DESC"
		: "c_entered";

	$RC = new ResComment();
	$comments = $RC->Get($params);

	# XXX : hack, vajag rādīt pa taisno foruma ierakstu
	if(($forum_data['forum_display'] == Forum::DISPLAY_DATA) && !empty($comments[0]))
	{
		# Ja sakārtots dilstoši, tad jāaiztiek ir pēdējais komments
		if(
			isset($_SESSION['login']['l_forumsort_msg']) &&
			($_SESSION['login']['l_forumsort_msg'] == Forum::SORT_DESC)
			)
		{
			array_unshift($comments, array_pop($comments));
		}
		$comments[0]['c_datacompiled'] = $forum_data['forum_data'];
	}

	$C = comment_list($template, $comments, $hl);

	$T->set_block_string('BLOCK_forum_comments', $C->parse());

	if($forum_data['forum_closed'] == Forum::CLOSED)
	{
		$T->disable('BLOCK_addcomment');
		$T->enable('BLOCK_forum_closed');
	}

	$forum->set_forum_path($T, $forum_id);

	return $T;
}

function comment_list(
	MainModule $template,
	array $comments,
	string $hl
){
	$C = $template->add_file('comments.tpl');

	if(user_loged())
	{
		$C->enable('BLOCK_addcomment');
		$C->set_var('acd_username', $_SESSION['login']['l_nick']);
		$C->set_var('c_username', $_SESSION['login']['l_nick'], 'BLOCK_addcomment');
		$disabled_users = CommentDisabled::get($_SESSION['login']['l_id']);
	} else {
		$C->enable('BLOCK_notloggedin');
		$disabled_users = array();
	}

	if($comments)
	{
		$C->enable('BLOCK_comment');
	} else {
		$C->enable('BLOCK_nocomment');
	}

	$comment_nr = 0;

	foreach($comments as $item)
	{
		$comment_nr++;
		$item['res_votes'] = (int)$item['res_votes'];
		# balsošana
		if(user_loged() && $C->block_exists('BLOCK_comment_vote')){
			$C->enable('BLOCK_comment_vote');
		}

		if($item['res_votes'] > 0)
		{
			$C->set_var('comment_vote_class', 'plus', 'BLOCK_comment');
			$item['res_votes'] = '+'.$item['res_votes'];
		} elseif($item['res_votes'] < 0) {
			$C->set_var('comment_vote_class', 'minus', 'BLOCK_comment');
		} else {
			$C->set_var('comment_vote_class', '', 'BLOCK_comment');
		}

		if($hl){
			hl($item['c_datacompiled'], $hl);
		}

		$item['c_username'] = parse_form_data($item['c_username']);
		if(empty($disabled_users[$item['login_id']])){
			$C->set_var('c_disabled_user_class', '');
		} else {
			$C->set_var('c_disabled_user_class', ' disabled');
			$item['c_datacompiled'] = '-neredzams komentārs-';
		}

		$C->set_array($item, 'BLOCK_comment');
	/*	if($item['c_entered'] == '2018-03-03 00:00:38'){
			$a = proc_date($item['c_entered']);
			printr($a);
		}
		*/
		$C->set_var('c_date', proc_date($item['c_entered']));

		// Joined from logins
		if($item['l_login'])
		$C->set_var('user_login_id', $item['l_login']);
		elseif($item['c_userlogin']) // legacy
		$C->set_var('user_login_id', $item['c_userlogin']);
		elseif($item['login_id']) // legacy
		$C->set_var('user_login_id', $item['login_id']);

		if(user_loged() && ($item['c_userlogin'] || $item['login_id']))
			$C->enable('BLOCK_profile_link');
		else
			$C->disable('BLOCK_profile_link');

		$C->set_var('comment_nr', $comment_nr, 'BLOCK_comment');

		$C->parse_block('BLOCK_comment_list', TMPL_APPEND);
	}

	return $C;
}

function get_attendees(int $res_id){
	global $db;

	$sql = "SELECT a.*, l.l_nick
	FROM attend a
	JOIN logins l ON l.l_id = a.l_id
	WHERE res_id = $res_id
	ORDER BY a_entered";

	return $db->Execute($sql);
}
