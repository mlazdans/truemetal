<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

define('FORUM_ACTIVE', 'Y');
define('FORUM_DEACTIVE', 'N');
define('FORUM_ALLOWCHILDS', 'Y');
define('FORUM_PROHIBITCHILDS', 'N');
define('FORUM_ALL', false);
define('FORUM_COMMENTS', true);
define('FORUM_VALIDATE', true);
define('FORUM_DONTVALIDATE', false);
define('FORUM_SORT_THEME', 'T');
define('FORUM_SORT_LAST_COMMENT', 'C');
define('FORUM_SORT_DESC', 'D');
define('FORUM_SORT_ASC', 'A');

class Forum
{
	var $date_format;
	var $page;
	var $fpp = 20;

	function Forum()
	{
		$this->date_format = '%Y:%m:%e:%H:%i';
	} // Forum

	function setPage($page)
	{
		$this->page = $page;
	} // setPage

	function load($forum_id = 0, $forum_forumid = 0, $forum_active = FORUM_ACTIVE, $order = '',
		$limit = '')
	{
		global $db;
/*
		if($GLOBALS['i_am_admin'])
		{
			$sql_cache = ' SQL_NO_CACHE ';
		} else {
			$sql_cache = '';
		}
*/
		$sql_cache = '';

		$field_b = '';
		$sql_add = '';
		if(!empty($_SESSION['login']['l_disable_bobi']))
		{
			$field_b = '_b';
			//$sql_add .= "f.forum_userid NOT IN (SELECT user_id FROM forum_badusers) AND ";
			$sql_add .= "fbu.user_id IS NULL AND ";
		}
		if($forum_id)
		{
			$sql_add .= "f.forum_id = $forum_id AND ";
		} else {
			$sql_add .= "f.forum_forumid = $forum_forumid AND ";
		}
		if($forum_active)
		{
			$sql_add .= "f.forum_active = '$forum_active' AND ";
		}
		$sql_add = substr($sql_add, 0, -5);

		$sql = "
SELECT
$sql_cache
	f.*, DATE_FORMAT(f.forum_entered, '".$this->date_format."') forum_date,
	fm.forum_childcount$field_b forum_childcount,
	fm.forum_lastcommentdate$field_b forum_lastcommentdate
FROM
	forum f
LEFT JOIN forum_meta fm ON fm.forum_id = f.forum_id";

		if(!empty($_SESSION['login']['l_disable_bobi']))
		{
			$sql .= "
LEFT OUTER JOIN forum_badusers fbu ON fbu.user_id = f.forum_userid";
		}

		if($sql_add)
		{
			$sql .= "
WHERE
	$sql_add";
		}


/*
		$sql.='
		GROUP BY
			f.forum_id,f.forum_forumid,f.forum_name,f.forum_active,f.forum_allowchilds,f.forum_data,f.forum_datacompiled,f.forum_entered,f.forum_username,f.forum_useremail,f.forum_userip';
*/
/*
		$sql.='
		GROUP BY
			f.forum_id
		';
*/
		if($order)
		{
			$sql .= "
ORDER BY
	$order";
		} else {
			$sql .= "
ORDER BY
	f.forum_entered DESC";
		}

		if($limit)
		{
			$sql .= ' '.$limit;
		} elseif($this->page) {
			$sql .= sprintf(" LIMIT %s,%s", ($this->page - 1) * $this->fpp, $this->fpp);
		}

		//if($GLOBALS['i_am_admin'])
		//{
		//	printr($sql);
		//}

		if($forum_id)
		{
			return $db->ExecuteSingle($sql);
		} else {
			return $db->Execute($sql);
		}
	} // load

	function getCount($forum_id = 0, $forum_forumid = 0, $forum_active = FORUM_ACTIVE, $order = '',
		$limit = '')
	{
		global $db;

		$sql = "SELECT * FROM forum_meta fm WHERE fm.forum_id = $forum_forumid";
		if($data = $db->ExecuteSingle($sql))
		{
			return empty($_SESSION['login']['l_disable_bobi']) ? $data['forum_childcount'] : $data['forum_childcount_b'];
		} else {
			return 0;
		}

		return $data ? (int)$data['item_count'] : 0;
	} // getCount

	function setItemsPerPage($fpp)
	{
		$this->fpp = $fpp;
	} // setItemsPerPage

	function get_tree($forum_id)
	{
		$forum_id = (integer)$forum_id;

		if(!$forum_id)
			return;

		$data = $this->load($forum_id);

		$data2 = array();
		if(isset($data['forum_forumid']))
			$data2 = $this->get_tree($data['forum_forumid']);

		if(!empty($data2) || !empty($data)) {
			$data2[] = $data;
		}

		return $data2;
	} // get_tree

	function get_all_tree($forum_id = 0, $d = 0)
	{
		if($d == 2)
		{
			return array();
		}

		$forum_id = (integer)$forum_id;

		$data2 = array();
		if($data = $this->load(0, $forum_id))
		{
			foreach($data as $item)
			{
				if(isset($item['forum_id']))
				{
					$tmp = $this->get_all_tree($item['forum_id'], $d + 1);
					if($tmp)
					{
						//$data2['_data_'] = $item;
						$data2[$item['forum_id']] = $tmp;
					}
					//$data2 = array_merge($data2, $tmp);
				}
			}
		}

		if(!empty($data2) || !empty($data))
		{
			$data2['_data_'] = $data;
		}

		return $data2;
	} // get_all_tree

	function set_all_tree(&$template, $tree, $forum_forumid = 0, $d = 0, $block = 'BLOCK_forum_forumid')
	{
		if($forum_forumid)
		{
			$data = &$tree[$forum_forumid]['_data_'];
		} else
			$data = &$tree['_data_'];

		if(isset($data))
		{
			foreach($data as $item)
			{
				//print str_repeat('&nbsp;', $d * 3).$item['forum_name']."\n";
				$template->set_var('new_forum_forumid', $item['forum_id'], $block);
				$template->set_var('new_forum_name', str_repeat('&nbsp;', $d * 3).$item['forum_name'], $block);
				$template->parse_block('BLOCK_forum_forumid', TMPL_APPEND);
				$this->set_all_tree($template, $tree, $item['forum_id'], $d + 1, $block);
			}
		}
	} // set_all_tree

	function load_by_userid($userid)
	{
		global $db;

		$userid = (int)$userid;

		$sql = "SELECT f1.*, f2.forum_name u_forum_name, f2.forum_id u_forum_id FROM forum f1, forum f2 WHERE f1.forum_userid = $userid AND f1.forum_forumid = f2.forum_id";

		return $db->Execute($sql);
	} // load_by_userid

	function add($forum_id, &$data, $validate = FORUM_DONTVALIDATE)
	{
		global $db, $ip;

		if(!user_loged())
		{
			$this->error_msg = 'Nav ielogojies!';
			return false;
		}

		if($validate)
			$this->validate($data);

		$forum_id = (integer)$forum_id;
//print "$forum_id:<br>";
		$forum = $this->load($forum_id);
//printr($forum);
		// ja apaksteema
		if($forum_id)
		{
			if(!isset($forum['forum_id']))
				return false;

			if($forum['forum_id'] != $forum_id)
				return false;

			if($forum['forum_active'] == FORUM_DEACTIVE)
				return false;
		}

		$user_id = $_SESSION['login']['l_id'];
		$user_login = $_SESSION['login']['l_login'];

		$sql = "
		INSERT INTO forum (
			forum_name, forum_username, forum_userid,
			forum_userlogin, forum_useremail, forum_userip, forum_entered,
			forum_forumid, forum_data, forum_datacompiled,
			forum_allowchilds, forum_active
		) VALUES (
			'$data[forum_name]', '$data[forum_username]', $user_id,
			'$user_login', '$data[forum_useremail]', '$ip', ".$db->now().",
			$forum_id, '$data[forum_data]', '$data[forum_datacompiled]',
			'$data[forum_allowchilds]', '$data[forum_active]'
		)";

		if($db->Execute($sql))
		{
			$_SESSION['user']['username'] = $data['forum_username'];
			$_SESSION['user']['useremail'] = $data['forum_useremail'];
			$id = last_insert_id();

			// ja pievieno teemu, ieliekam tajaa zinju
			if(isset($forum['forum_allowchilds']) && ($forum['forum_allowchilds'] == FORUM_ALLOWCHILDS))
			{
				//$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;
				$this->add($id, $data);
			}
//die;

			return $id;
		} else
			return false;
	} // add
/*
	function comment_count($forum_id, $forum_active = FORUM_ACTIVE)
	{
		global $db;

		if(!$forum_id)
			return 0;

		$sql = 'SELECT COUNT(*) comment_count FROM forum WHERE forum_forumid = '.$forum_id;

		if($forum_active)
			$sql .= ' AND forum_active = "'.$forum_active.'"';

		$data = $db->ExecuteSingle($sql);

		return (integer)$data['comment_count'];
	} // comment_count
	*/
/*
	function comment_count($forum_id, $forum_active = FORUM_ACTIVE)
	{
		global $db;

		if(!$forum_id)
			return 0;

		$sql = 'SELECT forum_id, forum_allowchilds, COUNT(*) comment_count FROM forum WHERE forum_forumid = '.$forum_id;

		if($forum_active)
			$sql .= ' AND forum_active = "'.$forum_active.'"';
		$sql .= ' GROUP BY forum_id, forum_allowchilds';

		$data = $db->Execute($sql);
		$count = 0;
		foreach($data as $item) {
			print_r($data);
			die;
			if($item['forum_allowchilds'] == FORUM_PROHIBITCHILDS)
				$count += $item['comment_count'];
			$count += $this->comment_count($item['forum_id']);
		}

		return $count;
	} // comment_count
	*/

	function save(&$data, $validate = FORUM_DONTVALIDATE)
	{
		global $db, $ip;

		if($validate)
			$this->validate($data);

		if(!$data['forum_id'])
			return true;

		$sql = 'UPDATE forum SET ';
		$sql .= $data['forum_name'] ? "forum_name = '$data[forum_name]', " : '';
		$sql .= $data['forum_entered'] ? "forum_entered = '$data[forum_entered]', " : '';
		$sql .= "forum_data = '$data[forum_data]', ";
		$sql .= "forum_allowchilds = '$data[forum_allowchilds]', ";
		$sql .= "forum_active = '$data[forum_active]', ";
		$sql .= $data['forum_datacompiled'] ? "forum_datacompiled = '$data[forum_datacompiled]', " : '';
		$sql = substr($sql, 0, -2);
		$sql .= 'WHERE forum_id = '.$data['forum_id'];

		return $db->Execute($sql);
	} // save

	function del_under($forum_id)
	{
		global $db;

		$forum_id = (integer)$forum_id;

		if(!$forum_id)
			return true;

		$ret = true;

		$sql = "SELECT forum_id FROM forum WHERE forum_forumid = ".$forum_id;
		$data = $db->Execute($sql);
		foreach($data as $item)
			$ret = $ret && $this->del($item['forum_id']);

		$sql = "DELETE FROM forum WHERE forum_forumid = ".$forum_id;

		return $ret && $db->Execute($sql);
	} // del_under

	function del($forum_id)
	{
		global $db;

		$forum_id = (integer)$forum_id;

		if(!$forum_id)
			return true;

		$ret = $this->del_under($forum_id);

		$sql = 'DELETE FROM forum WHERE forum_id = '.$forum_id;

		return $ret && $db->Execute($sql);
	} // del

	function activate($forum_id)
	{
		global $db;

		$forum_id = (integer)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "Y" WHERE forum_id = '.$forum_id;

		return $db->Execute($sql);
	} // activate

	function deactivate($forum_id)
	{
		global $db;

		$forum_id = (integer)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "N" WHERE forum_id = '.$forum_id;

		return $db->Execute($sql);
	} // deactivate

	function move($forum_id, $new_forum_forumid)
	{
		global $db;

		$forum_id = (integer)$forum_id;
		$new_forum_forumid = (integer)$new_forum_forumid;
		$sql = 'UPDATE forum SET forum_forumid = '.$new_forum_forumid.' WHERE forum_id = '.$forum_id;

		return $db->Execute($sql);
	} // deactivate

	function process_action(&$data, $action)
	{
		$ret = true;
		$func = '';

		if($action == 'delete_multiple')
			$func = 'del';

		if($action == 'activate_multiple')
			$func = 'activate';

		if($action == 'deactivate_multiple')
			$func = 'deactivate';

		if($action == 'move_multiple')
			$func = 'move';

		if(isset($data['item_count']) && $func)
			for($r = 1; $r <= $data['item_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['forum_checked'.$r]) && isset($data['forum_id'.$r]))
					if($func == 'move')
						$ret = $ret && $this->{$func}($data['forum_id'.$r], $data['new_forum_forumid']);
					else
						$ret = $ret && $this->{$func}($data['forum_id'.$r]);

		return $ret;
	} // process_action

	function validate(&$data)
	{
		if(isset($data['forum_id']))
			$data['forum_id'] = !ereg('[0-9]', $data['forum_id']) ? 0 : $data['forum_id'];
		else
			$data['forum_id'] = 0;

		if(isset($data['forum_forumid']))
			$data['forum_forumid'] = !ereg('[0-9]', $data['forum_forumid']) ? 0 : $data['forum_forumid'];
		else
			$data['forum_forumid'] = 0;

		if(isset($data['forum_active']))
			$data['forum_active'] = ereg('[^YN]', $data['forum_active']) ? FORUM_ACTIVE : $data['forum_active'];
		else
			$data['forum_active'] = FORUM_ACTIVE;

		if(isset($data['forum_allowchilds']))
			$data['forum_allowchilds'] = ereg('[^YN]', $data['forum_allowchilds']) ? FORUM_PROHIBITCHILDS : $data['forum_allowchilds'];
		else
			$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;

		if(!isset($data['forum_name']))
			$data['forum_name'] = '';
		else
			$data['forum_name'] = mb_strcut($data['forum_name'], 0, 60);

		if(!isset($data['forum_username']))
			$data['forum_username'] = '';

		if(!isset($data['forum_useremail']))
			$data['forum_useremail'] = '';

		if(!isset($data['forum_data']))
			$data['forum_data'] = '';

		if(!isset($data['forum_datacompiled']))
			$data['forum_datacompiled'] = $data['forum_data'];

		if(!isset($data['forum_entered']))
			$data['forum_entered'] = '';

		parse_text_data($data['forum_datacompiled']);

		my_strip_tags($data['forum_name']);
		my_strip_tags($data['forum_username']);
		my_strip_tags($data['forum_useremail']);

	} // validate

	function set_recent_forum(&$template)
	{
		global $db;

/*
		$sql_add1 = '';
		$sql_add2 = '';
		if(!empty($_SESSION['login']['l_disable_bobi']))
		{
			$sql_add1 .= " AND f.forum_userid NOT IN (".join(',', $bobijs).")";
			$sql_add2 .= " AND f2.forum_userid NOT IN (".join(',', $bobijs).")";
		}
		$sql = '
			SELECT
				f.forum_forumid, f2.forum_name up_forum, MAX(f.forum_entered) max_date,
				COUNT(f2.forum_forumid) comment_count
			FROM
				forum f USE INDEX ( forum_forumid )
			JOIN forum f2 ON (f2.forum_id = f.forum_forumid AND f2.forum_active = "'.FORUM_ACTIVE.'" AND f2.forum_allowchilds = "'.FORUM_PROHIBITCHILDS.'"'.$sql_add2.')
			WHERE
				f.forum_active = "'.FORUM_ACTIVE.'" AND f.forum_allowchilds = "'.FORUM_PROHIBITCHILDS.'"'.$sql_add1.'
			GROUP BY
				f.forum_forumid, f2.forum_name
			ORDER BY
				max_date DESC
			LIMIT 0,10
		';
*/
		$joins = '';
		$field_b = '';
		$sql_add = '';
		if(!empty($_SESSION['login']['l_disable_bobi']))
		{
			$joins .= "LEFT OUTER JOIN forum_badusers fbu ON fbu.user_id = f.forum_userid\n";
			//$sql_add .= " AND f.forum_userid NOT IN (SELECT user_id FROM forum_badusers)";
			$sql_add .= "fbu.user_id IS NULL AND ";
			$field_b = '_b';
		}

		$sql = "
SELECT
	f.forum_id, f.forum_forumid, f.forum_name,
	fm.forum_childcount$field_b forum_childcount,
	fm.forum_lastcommentdate$field_b forum_lastcommentdate
FROM
	forum_meta fm
JOIN forum f ON f.forum_id = fm.forum_id
$joins
WHERE
	$sql_add
	f.forum_active = '".FORUM_ACTIVE."' AND
	f.forum_allowchilds = '".FORUM_PROHIBITCHILDS."'
ORDER BY
	fm.forum_lastcommentdate$field_b DESC
LIMIT 0,10
		";
		$data = $db->Execute($sql);
/*
if($GLOBALS['sys_debug'])
{
	printr($sql);
}
*/
		if(count($data))
		{
			$template->set_file('FILE_r_forum', 'tmpl.forum_recent.php');
			$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_r_forum');
			foreach($data as $item)
			{
				$template->set_var('forum_r_name', $item['forum_name'], 'FILE_r_forum');
				//$template->set_var('forum_r_comment_count', $this->comment_count($item['forum_forumid']));
				$template->set_var('forum_r_comment_count', $item['forum_childcount'], 'FILE_r_forum');
				$template->set_var('forum_r_path', "forum/".$item['forum_id'], 'FILE_r_forum');
				$template->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
			}

			$template->parse_block('FILE_r_forum');
			$template->set_var('right_item_data', $template->get_parsed_content('FILE_r_forum'), 'BLOCK_right_item');
			$template->parse_block('BLOCK_right_item', TMPL_APPEND);
		}
	} // set_forum

	function search($q)
	{
		global $db;

		$forum_active = FORUM_ACTIVE;
		if($q)
			$search = search_to_sql($q, array('f.forum_data'));

		$sql = "
		SELECT
			COUNT(f.forum_forumid) matches,
			f2.forum_forumid,
			f2.forum_id,
			f2.forum_name
		FROM
			forum f
		JOIN forum f2 ON f2.forum_forumid = f.forum_id AND f2.forum_active = 'Y'
		WHERE
			$search AND f.forum_allowchilds = '".FORUM_PROHIBITCHILDS."'
		GROUP BY
			f.forum_forumid, f2.forum_name
		ORDER BY
			matches DESC
		LIMIT
			0,50
		";

		$data = $db->Execute($sql);

		return $data;
	}

} // Forum

