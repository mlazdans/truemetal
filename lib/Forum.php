<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/Res.php');
require_once('lib/Table.php');

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
define('FORUM_CLOSED', 'Y');
define('FORUM_OPEN', 'N');

class Forum extends Res
{
	const DISPLAY_DATACOMPILED = 0;
	const DISPLAY_DATA = 1;

	var $page;
	var $fpp = 20;

	protected $table_id = Table::FORUM;

	function __construct()
	{
		global $db;

		parent::__construct();

		$this->SetDb($db);
	} // __construct

	function setPage($page)
	{
		$this->page = $page;
	} // setPage

	function load(Array $params = array())
	{
		$sql_add = array();

		if(isset($params['forum_id']))
			$sql_add[] = "f.forum_id = $params[forum_id]";

		if(isset($params['res_id']))
			$sql_add[] = "f.res_id = $params[res_id]";

		if(isset($params['forum_ids']) && is_array($params['forum_ids']))
			$sql_add[] = sprintf("f.forum_id IN (%s)", join(",", $params['forum_ids']));

		if(isset($params['forum_forumid']))
			$sql_add[] = "f.forum_forumid = $params[forum_forumid]";

		if(isset($params['forum_active']))
		{
			if($params['forum_active'] != FORUM_ALL)
				$sql_add[] = sprintf("f.forum_active = '%s'", $params['forum_active']);
		} else {
			$sql_add[] = sprintf("f.forum_active = '%s'", FORUM_ACTIVE);
		}

		if(isset($params['forum_allowchilds']))
			$sql_add[] = sprintf("f.forum_allowchilds = '%s'", $params['forum_allowchilds']);

		$sql = "
SELECT
	f.*,
	r.*,
	COALESCE(res_comment_count, 0) AS forum_comment_count,
	res_comment_lastdate AS forum_lastcommentdate,
	(SELECT COUNT(*) FROM forum f2 WHERE f2.forum_forumid = f.forum_id) forum_themecount,
	(SELECT MAX(forum_entered) FROM forum f3 WHERE f3.forum_forumid = f.forum_id) forum_lastthemedate
FROM
	forum f
LEFT JOIN `res` r ON r.`res_id` = f.`res_id`
";

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= (empty($params['order']) ? " ORDER BY f.forum_entered DESC " : " ORDER BY $params[order] ");

		if(isset($params['limit']))
		{
			$sql .= " LIMIT $params[limit]";
		} else {
			if($this->page)
				$sql .= sprintf(" LIMIT %s,%s", ($this->page - 1) * $this->fpp, $this->fpp);
		}

		return (isset($params['forum_id']) || isset($params['res_id']) ? $this->db->ExecuteSingle($sql) : $this->db->Execute($sql));
	} // load

	function getThemeCount($forum_id = 0, $forum_active = FORUM_ACTIVE)
	{
		//global $db;

		$sql = "
SELECT
	COUNT(forum_id) AS forum_comment_count
FROM
	forum
WHERE
	forum_forumid = $forum_id AND
	forum_active = '$forum_active'";

		if($data = $this->db->ExecuteSingle($sql))
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
	function add($forum_id, &$data, $validate = FORUM_DONTVALIDATE, $forum_active = FORUM_ACTIVE)
	{
		//global $db, $ip;
		global $ip;

		if(!($res_id = parent::Add())) {
			return false;
		}

		if($validate)
			$this->validate($data);

		$forum_id = (int)$forum_id;
		$forum = $this->load(array(
			"forum_id"=>$forum_id,
			"forum_active"=>$forum_active,
			));

		// ja apaksteema
		if($forum_id)
		{
			if(!isset($forum['forum_id']))
				return false;

			if($forum['forum_id'] != $forum_id)
				return false;
		}

		$data['login_id'] = $data['login_id'] ? $data['login_id'] : "NULL";
		$data2 = $this->db->QuoteArray($data);

		$sql = "
INSERT INTO forum (
	res_id, forum_name, forum_username, login_id,
	forum_userlogin, forum_useremail, forum_userip, forum_entered,
	forum_forumid, forum_data, forum_datacompiled,
	forum_allowchilds, forum_active, forum_closed
) VALUES (
	$res_id, '$data2[forum_name]', '$data2[forum_username]', $data2[login_id],
	'$data2[forum_userlogin]', '$data2[forum_useremail]', '$ip', ".$this->db->now().",
	$forum_id, '$data2[forum_data]', '$data2[forum_datacompiled]',
	'$data2[forum_allowchilds]', '$data2[forum_active]', '$data2[forum_closed]'
)";

		return ($this->db->Execute($sql) ? $this->db->LastID() : false);
	} // add

	function save(&$data, $validate = FORUM_DONTVALIDATE)
	{
		//global $db;

		if($validate)
			$this->validate($data);

		if(!$data['forum_id'])
			return true;

		$data2 = $this->db->QuoteArray($data);

		$sql = 'UPDATE forum SET ';
		$sql .= $data2['forum_name'] ? "forum_name = '$data2[forum_name]', " : '';
		$sql .= $data2['forum_entered'] ? "forum_entered = '$data2[forum_entered]', " : '';
		$sql .= $data2['forum_datacompiled'] ? "forum_datacompiled = '$data2[forum_datacompiled]', " : '';
		$sql .= "forum_data = '$data2[forum_data]', ";
		$sql .= "forum_allowchilds = '$data2[forum_allowchilds]', ";
		$sql .= "forum_modid = $data2[forum_modid], ";
		$sql .= "forum_display = $data2[forum_display], ";

		$sql .= "forum_active = '$data2[forum_active]', ";
		$sql .= "forum_closed = '$data2[forum_closed]', ";
		$sql = substr($sql, 0, -2);
		$sql .= 'WHERE forum_id = '.$data2['forum_id'];

		return $this->db->Execute($sql);
	} // save

	function del_under($forum_id)
	{
		//global $db;

		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return true;

		$ret = true;

		$sql = "SELECT forum_id FROM forum WHERE forum_forumid = ".$forum_id;
		$data = $this->db->Execute($sql);
		foreach($data as $item)
			$ret = $ret && $this->del($item['forum_id']);

		$sql = "DELETE FROM forum WHERE forum_forumid = ".$forum_id;

		return $ret && $this->db->Execute($sql);
	} // del_under

	function del($forum_id)
	{
		//global $db;

		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return true;

		$ret = $this->del_under($forum_id);

		$sql = 'DELETE FROM forum WHERE forum_id = '.$forum_id;

		return $ret && $this->db->Execute($sql);
	} // del

	function open($forum_id)
	{
		//global $db;

		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_closed = "'.FORUM_OPEN.'" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // open

	function close($forum_id)
	{
		//global $db;

		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_closed = "'.FORUM_CLOSED.'" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // close

	function activate($forum_id)
	{
		//global $db;

		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "Y" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // activate

	function deactivate($forum_id)
	{
		//global $db;

		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "N" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // deactivate

	function move($forum_id, $new_forum_forumid)
	{
		//global $db;

		$forum_id = (int)$forum_id;
		$new_forum_forumid = (int)$new_forum_forumid;
		$sql = 'UPDATE forum SET forum_forumid = '.$new_forum_forumid.' WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
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

		if($action == 'close_multiple')
			$func = 'close';

		if($action == 'open_multiple')
			$func = 'open';

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
		$data['forum_id'] = isset($data['forum_id']) ? (int)$data['forum_id'] : 0;
		$data['forum_forumid'] = isset($data['forum_forumid']) ? (int)$data['forum_forumid'] : 0;
		$data['login_id'] = isset($data['login_id']) ? (int)$data['login_id'] : 0;
		$data['forum_modid'] = isset($data['forum_modid']) ? (int)$data['forum_modid'] : 0;
		$data['forum_display'] = isset($data['forum_display']) ? (int)$data['forum_display'] : 0;

		if(isset($data['forum_active']))
			$data['forum_active'] = preg_match('/[YN]/', $data['forum_active']) ? $data['forum_active'] : FORUM_ACTIVE;
		else
			$data['forum_active'] = FORUM_ACTIVE;

		if(isset($data['forum_closed']))
			$data['forum_closed'] = preg_match('/[YN]/', $data['forum_closed']) ? $data['forum_closed'] : FORUM_OPEN;
		else
			$data['forum_closed'] = FORUM_OPEN;

		if(isset($data['forum_allowchilds']))
			$data['forum_allowchilds'] = preg_match('/[YN]/', $data['forum_allowchilds']) ? $data['forum_allowchilds'] : FORUM_PROHIBITCHILDS;
		else
			$data['forum_allowchilds'] = FORUM_PROHIBITCHILDS;

		if(isset($data['forum_name']))
			$data['forum_name'] = mb_strcut($data['forum_name'], 0, 64);
		else
			$data['forum_name'] = '';

		if(!isset($data['forum_username']))
			$data['forum_username'] = '';

		if(!isset($data['forum_userlogin']))
			$data['forum_userlogin'] = '';

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
		$data = $this->load(array(
			"order"=>'forum_lastcommentdate DESC',
			"limit"=>'10',
			"forum_allowchilds"=>FORUM_PROHIBITCHILDS,
			));

		if(count($data))
		{
			$template->set_file('FILE_r_forum', 'forum/recent.tpl');
			$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_r_forum');
			foreach($data as $item)
			{
				$template->{(Forum::hasNewComments($item) ? "enable" : "disable")}('BLOCK_forum_r_comments_new');
				$template->set_var('forum_r_name', addslashes($item['forum_name']), 'FILE_r_forum');
				$template->set_var('forum_r_comment_count', $item['forum_comment_count'], 'FILE_r_forum');
				$template->set_var('forum_r_path', "forum/{$item['forum_id']}-".rawurlencode(urlize($item["forum_name"])), 'FILE_r_forum');
				$template->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
			}

			$template->enable('BLOCK_forum_r_more');
			$template->parse_block('FILE_r_forum');
			$template->set_var('right_item_data', $template->get_parsed_content('FILE_r_forum'), 'BLOCK_right_item');
			$template->parse_block('BLOCK_right_item', TMPL_APPEND);
		}
	} // set_recent_forum

	static function hasNewComments($item)
	{
		if(isset($_SESSION['forums']['viewed'][$item['forum_id']]))
			return ($item['forum_comment_count'] > $_SESSION['forums']['viewed'][$item['forum_id']]);

		if(isset($_SESSION['forums']['viewed_before']))
			return ($_SESSION['forums']['viewed_before'] < strtotime($item['forum_lastcommentdate']));

		return true;
	} // hasNewComments

	static function hasNewThemes($item)
	{
		if(isset($_SESSION['forums']['viewed'][$item['forum_id']]))
			return ($item['forum_themecount'] > $_SESSION['forums']['viewed'][$item['forum_id']]);

		if(isset($_SESSION['forums']['viewed_before']))
			return ($_SESSION['forums']['viewed_before'] < strtotime($item['forum_lastthemedate']));

		return true;
	} // hasNewThemes

} // Class::Forum

