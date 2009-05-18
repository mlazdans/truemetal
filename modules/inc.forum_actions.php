<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// 

$template->set_var('forumd_name', '', 'FILE_forum');
$template->set_var('forumd_data', '', 'FILE_forum');

if(isset($_POST['data']) && user_loged())
{
	$error = false;

	if($action == 'add_item')
	{
		$data = $_POST['data'];
		$data['forum_username'] = $_SESSION['login']['l_nick'];
		$forum->validate($data);
		$template->set_var('forumd_username', $data['forum_username'], 'FILE_forum');
		$template->set_var('forumd_useremail', $data['forum_useremail'], 'FILE_forum');
		$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;

		if(!$data['forum_datacompiled'])
		{
			$error = true;
			$template->enable('BLOCK_forumdata_error');
		}

		if(!$data['forum_username'])
			$data['forum_username'] = '-anonīms-';

		if(!$error)
		{
			if($id = $forum->add($forum_id, $data))
			{
				header("Location: $module_root/$forum_id/#comment$id");
				exit;
			}
		}
	}

	if($action == 'add_theme')
	{
		$data = $_POST['data'];
		$forum->validate($data);
		$data['forum_username'] = $_SESSION['login']['l_nick'];
		$template->set_var('forumd_username', $data['forum_username'], 'FILE_forum');
		$template->set_var('forumd_useremail', $data['forum_useremail'], 'FILE_forum');
		$template->set_var('forumd_name', parse_form_data($data['forum_name']), 'FILE_forum');
		$template->set_var('forumd_data', parse_form_data($data['forum_data']), 'FILE_forum');

		$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;

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

		if(!$data['forum_username'])
			$data['forum_username'] = '-anonīms-';

		if(!$error)
		{
			if($id = $forum->add($forum_id, $data))
			{
				header("Location: $module_root/$id/");
				exit;
			}
		}
	}

} // post data && forum_id

?>