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
	var $page;
	var $fpp = 20;

	function __construct()
	{
	} // __construct

	function setPage($page)
	{
		$this->page = $page;
	} // setPage

	/*
	function load($forum_id = 0, $forum_forumid = 0, $forum_active = FORUM_ACTIVE, $order = '',
		$limit = '')
	{
	*/
	function load(Array $params = array())
	{
		global $db;

		$sql_add = array();

		if(isset($params['forum_id']))
			$sql_add[] = "f.forum_id = $params[forum_id]";

		if(isset($params['forum_forumid']))
			$sql_add[] = "f.forum_forumid = $params[forum_forumid]";

		if(isset($params['forum_active']))
			$sql_add[] = "f.forum_active = '$forum_active'";
		else
			$sql_add[] = sprintf("f.forum_active = '%s'", FORUM_ACTIVE);

		if(isset($params['forum_allowchilds']))
			$sql_add[] = sprintf("f.forum_allowchilds = '%s'", $params['forum_allowchilds']);

		//(SELECT COUNT(c_id) FROM comment JOIN comment_connect ON cc_c_id = c_id WHERE cc_table = 'forum' AND cc_table_id = f.forum_id) forum_comment_count,
		//(SELECT MAX(c_entered) FROM comment JOIN comment_connect ON cc_c_id = c_id WHERE cc_table = 'forum' AND cc_table_id = f.forum_id) forum_lastcommentdate
		//$sql_add[] = "cm_table_id = f.forum_id";
		$sql = "
SELECT
	f.*,
	COALESCE(cm_comment_count, 0) AS forum_comment_count,
	cm_comment_lastdate AS forum_lastcommentdate,
	(SELECT COUNT(*) FROM forum f2 WHERE f2.forum_forumid = f.forum_id) forum_themecount
FROM
	forum f
LEFT JOIN comment_meta ON (cm_table = 'forum') AND (cm_table_id = f.forum_id)";

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= (empty($params['order']) ? " ORDER BY f.forum_entered DESC " : " ORDER BY $params[order] ");

		if(empty($params['limit']))
		{
			if($this->page)
				$sql .= sprintf(" LIMIT %s,%s", ($this->page - 1) * $this->fpp, $this->fpp);
		} else {
			$sql .= ' '.$params['limit'];
		}

		return (isset($params['forum_id']) ? $db->ExecuteSingle($sql) : $db->Execute($sql));
	} // load

	function getThemeCount($forum_id = 0, $forum_active = FORUM_ACTIVE)
	{
		global $db;

		$sql = "
SELECT
	COUNT(forum_id) AS forum_comment_count
FROM
	forum
WHERE
	forum_forumid = $forum_id AND
	forum_active = '$forum_active'";

		if($data = $db->ExecuteSingle($sql))
		{
			return $data['forum_comment_count'];
		} else {
			return 0;
		}
	} // getThemeCount

	function setItemsPerPage($fpp)
	{
		$this->fpp = $fpp;
	} // setItemsPerPage

	function get_tree($forum_id)
	{
		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return;

		$data = $this->load(array(
			"forum_id"=>$forum_id,
			));

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

		$forum_id = (int)$forum_id;

		$data2 = array();
		//if($data = $this->load(0, $forum_id))
		$data = $this->load(array(
			"forum_forumid"=>$forum_id
			));

		if($data)
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
/*
	function load_by_userid($userid)
	{
		global $db;

		$userid = (int)$userid;

		$sql = "SELECT f1.*, f2.forum_name u_forum_name, f2.forum_id u_forum_id FROM forum f1, forum f2 WHERE f1.forum_userid = $userid AND f1.forum_forumid = f2.forum_id";

		return $db->Execute($sql);
	} // load_by_userid
*/
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

		$forum_id = (int)$forum_id;
		$forum = $this->load(array(
			"forum_id"=>$forum_id
			));

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

		return (int)$data['comment_count'];
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

		$forum_id = (int)$forum_id;

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

		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return true;

		$ret = $this->del_under($forum_id);

		$sql = 'DELETE FROM forum WHERE forum_id = '.$forum_id;

		return $ret && $db->Execute($sql);
	} // del

	function activate($forum_id)
	{
		global $db;

		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "Y" WHERE forum_id = '.$forum_id;

		return $db->Execute($sql);
	} // activate

	function deactivate($forum_id)
	{
		global $db;

		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "N" WHERE forum_id = '.$forum_id;

		return $db->Execute($sql);
	} // deactivate

	function move($forum_id, $new_forum_forumid)
	{
		global $db;

		$forum_id = (int)$forum_id;
		$new_forum_forumid = (int)$new_forum_forumid;
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

		$data = $this->load(array(
			"order"=>'forum_lastcommentdate DESC',
			"limit"=>'LIMIT 0,10',
			"forum_allowchilds"=>FORUM_PROHIBITCHILDS,
			));

		if(count($data))
		{
			$template->set_file('FILE_r_forum', 'tmpl.forum_recent.php');
			$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_r_forum');
			foreach($data as $item)
			{
				$template->set_var('forum_r_name', $item['forum_name'], 'FILE_r_forum');
				//$template->set_var('forum_r_comment_count', $this->comment_count($item['forum_forumid']));
				$template->set_var('forum_r_comment_count', $item['forum_comment_count'], 'FILE_r_forum');
				$template->set_var('forum_r_path', "forum/".$item['forum_id'], 'FILE_r_forum');
				$template->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
			}

			$template->parse_block('FILE_r_forum');
			$template->set_var('right_item_data', $template->get_parsed_content('FILE_r_forum'), 'BLOCK_right_item');
			$template->parse_block('BLOCK_right_item', TMPL_APPEND);
		}
	} // set_recent_forum
/*
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
*/
} // Forum

