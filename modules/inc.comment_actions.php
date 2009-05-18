<?
if(isset($item['art_id']) && $item['art_id'] == $art_id)
{
	if($action == 'add_comment' && ($item['art_comments'] == ARTICLE_COMMENTS) && user_loged())
	{
		if(!isset($_POST['data'])) {
			header("Location: $sys_http_root/");
			exit;
		}

		$data = $_POST['data'];
		$data['ac_username'] = $_SESSION['login']['l_nick'];
		$article->validate_comment($data);

		if(!$data['ac_data'] || !$data['ac_username'])
		{
			//$template->enable('BLOCK_acdata_error');
			$template->enable('BLOCK_comment_error');
			$template->set_var('error_msg', 'Nekorekti aizpildīta forma!', 'BLOCK_comment_error');
			$template->set_var('acd_username', $data['ac_username']);
			$template->set_var('acd_useremail', $data['ac_useremail']);
		} else
			if($ac_id = $article->add_comment($art_id, $data, ARTICLE_DONTVALIDATE))
			{
				//header("Location: $sys_http_root/article/$art_id/");
				$np = join('/', array_keys($path));
				header("Location: $sys_http_root/$np/$art_id/#comment$ac_id");
				exit;
			} else {
				$template->enable('BLOCK_comment_error');
				$template->set_var('error_msg', $article->error_msg);
			}
	} // add_comment

	if(user_loged())
	{
		$template->enable('BLOCK_loggedin');
		$template->set_var('acd_username', $_SESSION['login']['l_nick']);
	} else {
		$template->enable('BLOCK_notloggedin');
	}

	// ja ir atdaliitaajs (ivads->turpinaajums)
	//$patt = '/(.*)<hr\s+id=editor_splitter>(.*)/ims';
	//$item['art_data'] = preg_replace($patt, '<b>\\1</b><hr>\\2', $item['art_data'], 1);
	$patt = '/(.*)(<hr\s+id=editor_splitter>)(.*)/ims';
	$item['art_data'] = preg_replace($patt, '<b>\1</b><hr>\3', $item['art_data'], 1);
	//print_r($item);
	//die();

	$item['art_date'] = proc_date($item['art_entered']);
	$template->set_array($item);
	$template->set_title('Komentāri - '.$item['art_name']);

	$comment_count = $article->comment_count($art_id);

	if($item['art_comments'] == ARTICLE_NOCOMMENTS)
		$template->disable('BLOCK_is_comments');

	if($comment_count && ($item['art_comments'] == ARTICLE_COMMENTS))
	{
		// saglabaajam raksta komentaaru skaitu, lai veelaak
		// vareetu piefixeet, vai ir jauni vai nee
		/*
		if(isset($_SESSION['comments']['viewed'][$art_id]))
		{
			if($_SESSION['comments']['viewed'][$art_id] < $comment_count)
			{
				update_cache_module($item['module_id']);
			}
		} else {
			update_cache_module($item['module_id']);
		}
		*/
		$_SESSION['comments']['viewed'][$art_id] = $comment_count;

		$template->enable('BLOCK_comment');
		$comments = $article->load_comments($art_id);

		foreach($comments as $item)
		{
			# balsošana
			if(user_loged() && $template->block_isset('BLOCK_article_comment_vote'))
			{
				$template->enable('BLOCK_article_comment_vote');
				if($item['ac_votes'] > 0)
				{
					$template->set_var('comment_vote_class', 'Comment-Vote-plus', 'BLOCK_comment');
					$item['ac_votes'] = '+'.$item['ac_votes'];
				} elseif($item['ac_votes'] < 0) {
					$template->set_var('comment_vote_class', 'Comment-Vote-minus', 'BLOCK_comment');
				} else {
					$template->set_var('comment_vote_class', 'Comment-Vote', 'BLOCK_comment');
				}
			}

			$template->set_array($item);
			$template->set_var('ac_date', proc_date($item['ac_entered']));

			if($item['ac_useremail'])
				$template->enable('BLOCK_email');
			else
				$template->disable('BLOCK_email');

			if($item['ac_userlogin'])
				$template->set_var('user_login_id', $item['ac_userlogin']);
			elseif($item['ac_userid'])
				$template->set_var('user_login_id', $item['ac_userid']);

			if($item['ac_userlogin'] || $item['ac_userid'])
				$template->enable('BLOCK_profile_link');
			else
				$template->disable('BLOCK_profile_link');

			$template->parse_block('BLOCK_comment', TMPL_APPEND);
		} // foreach

	} else
		$template->enable('BLOCK_nocomment');
} // isset $art_id ....
?>
