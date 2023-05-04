<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Res.php');
require_once('lib/Table.php');

class Forum extends Res
{
	const DISPLAY_DATACOMPILED = 0;
	const DISPLAY_DATA = 1;

	const SORT_THEME = 'T';
	const SORT_LASTCOMMENT = 'C';
	const SORT_DESC = 'D';
	const SORT_ASC = 'A';

	const CLOSED = 'Y';
	const OPEN = 'N';

	const ALLOW_CHILDS = 'Y';
	const PROHIBIT_CHILDS = 'N';

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

		if(isset($params['type_id']))
			$sql_add[] = "f.type_id = $params[type_id]";

		if(isset($params['res_id']))
			$sql_add[] = "f.res_id = $params[res_id]";

		if(isset($params['actual_events'])){
			$sql_add[] = "f.type_id = ".Res::TYPE_EVENT;
			$sql_add[] = sprintf("f.event_startdate >= '%s'", date('Y-m-d'));
		}

		if(isset($params['forum_ids']) && is_array($params['forum_ids']))
			$sql_add[] = sprintf("f.forum_id IN (%s)", join(",", $params['forum_ids']));

		if(isset($params['res_ids']) && is_array($params['res_ids']))
			$sql_add[] = sprintf("f.res_id IN (%s)", join(",", $params['res_ids']));

		if(isset($params['forum_forumid'])){
			if($params['forum_forumid'] == 0){
				$sql_add[] = "f.forum_forumid IS NULL";
			} else {
				$sql_add[] = "f.forum_forumid = $params[forum_forumid]";
			}
		}

		if(isset($params['forum_active']))
		{
			if($params['forum_active'] != Res::STATE_ALL)
				$sql_add[] = sprintf("f.forum_active = '%s'", $params['forum_active']);
		} else {
			$sql_add[] = sprintf("f.forum_active = '%s'", Res::STATE_ACTIVE);
		}

		if(isset($params['forum_allowchilds']))
			$sql_add[] = sprintf("f.forum_allowchilds = '%s'", $params['forum_allowchilds']);

		if(isset($params['fields'])){
			$sql = "SELECT ".join(',', $params['fields']);
		} else {
			$sql = "SELECT f.*, r.*";
		}

		# TODO: forum_themecount, forum_lastthemedate trigeros
		$sql .= ",
	COALESCE(res_comment_count, 0) AS res_comment_count,
	res_comment_lastdate,
	(SELECT COUNT(*) FROM forum f2 WHERE f2.forum_forumid = f.forum_id AND f2.forum_active = '".Res::STATE_ACTIVE."') forum_themecount,
	(SELECT MAX(forum_entered) FROM forum f3 WHERE f3.forum_forumid = f.forum_id AND f3.forum_active = '".Res::STATE_ACTIVE."') forum_lastthemedate
FROM
	forum f
	JOIN `res` r ON r.`res_id` = f.`res_id`
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

	function Add()
	{
		global $ip;

		list($forum_id, $data, $validate, $forum_active) = func_get_args();

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

		$this->login_id = $data['login_id'];
		if(!($res_id = parent::Add())) {
			return false;
		}

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

	function save(&$data, $validate = Res::ACT_DONTVALIDATE)
	{
		if($validate)
			$this->validate($data);

		if(!$data['forum_id'])
			return true;

		$data2 = $this->db->QuoteArray($data);

		$sql = 'UPDATE forum SET ';
		$sql .= $data2['forum_name'] ? "forum_name = '$data2[forum_name]', " : '';
		$sql .= $data2['forum_entered'] ? "forum_entered = '$data2[forum_entered]', " : '';
		$sql .= $data2['forum_datacompiled'] ? "forum_datacompiled = '$data2[forum_datacompiled]', " : '';
		$sql .= isset($data2['type_id']) ? "type_id = $data2[type_id], " : '';
		$sql .= $data2['event_startdate'] ? "event_startdate = '$data2[event_startdate]', " : '';
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
		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return true;

		$ret = $this->del_under($forum_id);

		$sql = 'DELETE FROM forum WHERE forum_id = '.$forum_id;

		return $ret && $this->db->Execute($sql);
	} // del

	function open($forum_id)
	{
		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_closed = "'.Forum::OPEN.'" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // open

	function close($forum_id)
	{
		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_closed = "'.Forum::CLOSED.'" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // close

	function activate($forum_id)
	{
		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "Y" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // activate

	function deactivate($forum_id)
	{
		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "N" WHERE forum_id = '.$forum_id;

		return $this->db->Execute($sql);
	} // deactivate

	function move($forum_id, $new_forum_forumid)
	{
		$forum_id = (int)$forum_id;
		$new_forum_forumid = (int)$new_forum_forumid;
		if($new_forum_forumid == 0)
			$new_forum_forumid = "NULL";

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

		if(!empty($data['forum_checked']) && $func)
		{
			foreach($data['forum_checked'] as $forum_id=>$on){
				if($func == 'move')
					$ret = $ret && $this->{$func}($forum_id, $data['new_forum_forumid']);
				else
					$ret = $ret && $this->{$func}($forum_id);
			}
		}

		return $ret;
	} // process_action

	function validate(&$data)
	{
		$data['forum_id'] = isset($data['forum_id']) ? (int)$data['forum_id'] : 0;
		$data['forum_forumid'] = isset($data['forum_forumid']) ? (int)$data['forum_forumid'] : "NULL";
		$data['login_id'] = isset($data['login_id']) ? (int)$data['login_id'] : 0;
		$data['forum_modid'] = isset($data['forum_modid']) ? (int)$data['forum_modid'] : 0;
		$data['forum_display'] = isset($data['forum_display']) ? (int)$data['forum_display'] : 0;

		if(isset($data['event_startdate'])){
			$data['event_startdate'] = date('Y-m-d', strtotime($data['event_startdate']));
		}

		if(isset($data['forum_active']))
			$data['forum_active'] = preg_match('/[YN]/', $data['forum_active']) ? $data['forum_active'] : Res::STATE_ACTIVE;
		else
			$data['forum_active'] = Res::STATE_ACTIVE;

		if(isset($data['forum_closed']))
			$data['forum_closed'] = preg_match('/[YN]/', $data['forum_closed']) ? $data['forum_closed'] : Forum::OPEN;
		else
			$data['forum_closed'] = Forum::OPEN;

		if(isset($data['forum_allowchilds']))
			$data['forum_allowchilds'] = preg_match('/[YN]/', $data['forum_allowchilds']) ? $data['forum_allowchilds'] : Forum::PROHIBIT_CHILDS;
		else
			$data['forum_allowchilds'] = Forum::PROHIBIT_CHILDS;

		if(isset($data['forum_name']))
			$data['forum_name'] = mb_strcut($data['forum_name'], 0, 128);
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

	function set_recent_forum(Template $template)
	{
		$data = $this->load(array(
			"fields"=>array('forum_id', 'forum_name', 'f.res_id'),
			"order"=>'res_comment_lastdate DESC',
			"limit"=>'10',
			"forum_allowchilds"=>Forum::PROHIBIT_CHILDS,
			));

		if(count($data))
		{
			$template->set_file('FILE_r_forum', 'forum/recent.tpl');
			foreach($data as $item)
			{
				$template->{(Forum::hasNewComments($item) ? "enable" : "disable")}('BLOCK_forum_r_comments_new');
				$template->set_var('forum_r_name', addslashes($item['forum_name']), 'FILE_r_forum');
				$template->set_var('forum_r_comment_count', $item['res_comment_count'], 'FILE_r_forum');
				$template->set_var('forum_r_path', "forum/{$item['forum_id']}-".rawurlencode(urlize($item["forum_name"])), 'FILE_r_forum');
				$template->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
			}

			$template->enable('BLOCK_forum_r_more');
			$template->parse_block('FILE_r_forum');
			$template->set_var('right_item_data', $template->get_parsed_content('FILE_r_forum'), 'BLOCK_right_item');
			$template->parse_block('BLOCK_right_item', TMPL_APPEND);
		}
	} // set_recent_forum

	public function set_all_tree(Template $template, $tree, $forum_forumid = 0, $d = 0, $block = 'BLOCK_forum_forumid')
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

	public function set_forum_path(Template $template, $forum_id)
	{
		if(!($tree = $this->get_tree($forum_id)))
			return false;

		$template->enable('BLOCK_forum_path');
		foreach($tree as $key=>$item)
		{
			if(isset($tree[$key + 0]))
			{
				$forum_path = Forum::Route($item);
				$template->set_var('forum_name', addslashes($item['forum_name']), 'BLOCK_forum_path');
				$template->set_var('forum_path', $forum_path, 'BLOCK_forum_path');
				$template->parse_block('BLOCK_forum_path', TMPL_APPEND);
			}
		}

		return true;
	} // set_forum_path

	public static function hasNewThemes($item)
	{
		if(!user_loged()){
			return false;
		}

		if(isset($_SESSION['forums']['viewed_date'][$item['forum_id']]))
			return (strtotime($item['forum_lastthemedate']) > strtotime($_SESSION['forums']['viewed_date'][$item['forum_id']]));

		if(isset($_SESSION['forums']['viewed'][$item['forum_id']]))
			return ($item['forum_themecount'] > $_SESSION['forums']['viewed'][$item['forum_id']]);

		if(isset($_SESSION['res']['viewed_before']))
			return ($_SESSION['res']['viewed_before'] < strtotime($item['forum_lastthemedate']));

		return ($item['forum_themecount'] > 0);
	} // hasNewThemes

	public static function markThemeCount($item)
	{
		if(!user_loged()){
			return false;
		}
		$_SESSION['forums']['viewed_date'][$item['forum_id']] = $item['forum_lastthemedate'];
	} // markThemeCount

	public static function Route($resource, $c_id = 0)
	{
		return "/forum/$resource[forum_id]-".urlize($resource['forum_name']).($c_id ? "#comment$c_id" : "");
	} // Route
} // Class::Forum

