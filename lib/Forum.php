<?php declare(strict_types = 1);

use dqdp\SQL\Select;
use dqdp\Template;
use dqdp\TODO;

class Forum implements ResourceInterface
{
	const DISPLAY_DATACOMPILED = 0;
	const DISPLAY_DATA = 1;

	const SORT_THEME = 'T';
	const SORT_LASTCOMMENT = 'C';
	const SORT_DESC = 'D';
	const SORT_ASC = 'A';

	static function load(array $params)
	{
		$sql = (new Select)
			->From('res')
			->Join('res_meta', 'res_meta.res_id = res.res_id')
			->Join('forum', 'forum.res_id = res.res_id')
		;

		if(isset($params['fields'])){
			$sql->Select(join(',', $params['fields']));
		} else {
			$sql->Select("forum.*, res.*, res_meta.*");
		}

		join_logins($sql);

		# TODO: abstract out
		$fields = ['forum_id', 'type_id', 'res_id'];
		foreach($fields as $f){
			if(isset($params[$f])){
				$sql->Where(["forum.$f = ?", $params[$f]]);
			}
		}

		if(isset($params['actual_events'])){
			$sql->Where(["forum.type_id = ?", Res::TYPE_EVENT]);
			$sql->Where(["forum.event_startdate >= ?", date('Y-m-d')]);
		}

		// if(isset($params['forum_ids'])){
		// 	$sql->WhereIn("forum.forum_id", $params['forum_ids']);
		// }

		if(isset($params['res_ids'])){
			$sql->WhereIn("forum.res_id", $params['res_id']);
		}

		if(isset($params['forum_forumid'])){
			throw new TODO('forum_forumid uz res_resid');
		}

		if(isset($params['res_resid'])){
			// throw new TODO('$params[forum_forumid]');
			# TODO: izdomāt kā labāk padot NULL
			if(empty($params['res_resid'])){
				$sql->Where("res.res_resid IS NULL");
			} else {
				$sql->Where(["res.res_resid = ?", $params['res_resid']]);
			}
		}

		if(isset($params['forum_active'])){
			new TODO('forum_active: use res_visible');
		}

		if(defaulted($params, 'res_visible'))
		{
			$sql->Where("res.res_visible = 1");
		} elseif(!ignored($params, 'res_visible')){
			$sql->Where(["res.res_visible = ?", $params['res_visible']]);
		}

		// if(defaulted($params, 'forum_active'))
		// {
		// 	$sql->Where("res.res_visible = 1");
		// } elseif(!ignored($params, 'forum_active')){
		// 	$sql->Where(["res.res_visible = ?", $params['forum_active']]);
		// }

		if(isset($params['forum_allow_childs'])){
			$sql->Where(["forum.forum_allow_childs = ?", $params['forum_allow_childs']]);
		}

		if(empty($params['order'])){
			$sql->OrderBy("res.res_entered DESC");
		} else {
			$sql->OrderBy($params['order']);
		}
		// $sql .= (empty($params['order']) ? " ORDER BY f.forum_entered DESC " : " ORDER BY $params[order] ");

		if(isset($params['rows']))
		{
			$sql->Rows((int)$params['rows']);
		}

		if(isset($params['limit']))
		{
			new TODO("Nodalīt rows un offset");
			// $sql .= " LIMIT $params[limit]";
		}

		if(isset($params['page']) && isset($params['items_per_page'])){
			$sql->Page((int)$params['page'], (int)$params['items_per_page']);
			// $sql .= sprintf(" LIMIT %s,%s", ($this->page - 1) * $this->fpp, $this->fpp);
		}

		return (isset($params['forum_id']) || isset($params['res_id']) ? DB::ExecuteSingle($sql) : DB::Execute($sql));
	}

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
	}

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
	}

// 	function Add()
// 	{
// 		global $ip;

// 		$args = func_get_args();
// 		$forum_id = (int)$args[0]??null;
// 		$data = $args[1]??null;
// 		$validate = $args[2]??null;
// 		$forum_active = $args[3]??null;

// 		if($validate)
// 			$this->validate($data);

// 		$forum_id = (int)$forum_id;
// 		$forum = $this->load(array(
// 			"forum_id"=>$forum_id,
// 			"forum_active"=>$forum_active,
// 			));

// 		// ja apaksteema
// 		if($forum_id)
// 		{
// 			if(!isset($forum['forum_id']))
// 				return false;

// 			if($forum['forum_id'] != $forum_id)
// 				return false;
// 		}

// 		$data['login_id'] = $data['login_id'] ? $data['login_id'] : "NULL";
// 		$data2 = DB::Quote($data);

// 		$this->login_id = $data['login_id'];
// 		new TODO("get res_id");
// 		// if(!($res_id = parent::Add())) {
// 		// 	return false;
// 		// }

// 		$sql = "
// INSERT INTO forum (
// 	res_id, forum_name, forum_username, login_id,
// 	forum_userlogin, forum_useremail, forum_userip, forum_entered,
// 	forum_forumid, forum_data, forum_datacompiled,
// 	forum_allowchilds, forum_active, forum_closed
// ) VALUES (
// 	$res_id, '$data2[forum_name]', '$data2[forum_username]', $data2[login_id],
// 	'$data2[forum_userlogin]', '$data2[forum_useremail]', '$ip', ".DB::now().",
// 	$forum_id, '$data2[forum_data]', '$data2[forum_datacompiled]',
// 	'$data2[forum_allowchilds]', '$data2[forum_active]', '$data2[forum_closed]'
// )";

// 		return (DB::Execute($sql) ? DB::LastID() : false);
// 	}

// 	function save(&$data, $validate = Res::ACT_DONTVALIDATE)
// 	{
// 		if($validate)
// 			$this->validate($data);

// 		if(!$data['forum_id'])
// 			return true;

// 		$data2 = DB::Quote($data);

// 		$sql = 'UPDATE forum SET ';
// 		$sql .= $data2['forum_name'] ? "forum_name = '$data2[forum_name]', " : '';
// 		$sql .= $data2['forum_entered'] ? "forum_entered = '$data2[forum_entered]', " : '';
// 		$sql .= $data2['forum_datacompiled'] ? "forum_datacompiled = '$data2[forum_datacompiled]', " : '';
// 		$sql .= isset($data2['type_id']) ? "type_id = $data2[type_id], " : '';
// 		$sql .= $data2['event_startdate'] ? "event_startdate = '$data2[event_startdate]', " : '';
// 		$sql .= "forum_data = '$data2[forum_data]', ";
// 		$sql .= "forum_allowchilds = '$data2[forum_allowchilds]', ";
// 		$sql .= "forum_modid = $data2[forum_modid], ";
// 		$sql .= "forum_display = $data2[forum_display], ";

// 		$sql .= "forum_active = '$data2[forum_active]', ";
// 		$sql .= "forum_closed = '$data2[forum_closed]', ";
// 		$sql = substr($sql, 0, -2);
// 		$sql .= 'WHERE forum_id = '.$data2['forum_id'];

// 		return DB::Execute($sql);
// 	}

	function del_under($forum_id)
	{
		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return true;

		$ret = true;

		$sql = "SELECT forum_id FROM forum WHERE forum_forumid = ".$forum_id;
		$data = DB::Execute($sql);
		foreach($data as $item)
			$ret = $ret && $this->del($item['forum_id']);

		$sql = "DELETE FROM forum WHERE forum_forumid = ".$forum_id;

		return $ret && DB::Execute($sql);
	}

	function del($forum_id)
	{
		$forum_id = (int)$forum_id;

		if(!$forum_id)
			return true;

		$ret = $this->del_under($forum_id);

		$sql = 'DELETE FROM forum WHERE forum_id = '.$forum_id;

		return $ret && DB::Execute($sql);
	}

	function open(int $forum_id): bool
	{
		$sql = 'UPDATE forum SET forum_closed = 0 WHERE forum_id = ?';

		return DB::Execute($sql, $forum_id);
	}

	function close(int $forum_id): bool
	{
		$sql = "UPDATE forum SET forum_closed = 1 WHERE forum_id = ?";

		return DB::Execute($sql, $forum_id);
	}

	function activate($forum_id)
	{
		new TODO("via res_visible");
		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "Y" WHERE forum_id = '.$forum_id;

		return DB::Execute($sql);
	}

	function deactivate($forum_id)
	{
		new TODO("via res_visible");
		$forum_id = (int)$forum_id;
		$sql = 'UPDATE forum SET forum_active = "N" WHERE forum_id = '.$forum_id;

		return DB::Execute($sql);
	}

	function move($forum_id, $new_forum_forumid)
	{
		$forum_id = (int)$forum_id;
		$new_forum_forumid = (int)$new_forum_forumid;
		if($new_forum_forumid == 0)
			$new_forum_forumid = "NULL";

		$sql = 'UPDATE forum SET forum_forumid = '.$new_forum_forumid.' WHERE forum_id = '.$forum_id;

		return DB::Execute($sql);
	}

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
	}

	// function validate(&$data)
	// {
	// 	$data['forum_id'] = isset($data['forum_id']) ? (int)$data['forum_id'] : 0;
	// 	$data['forum_forumid'] = isset($data['forum_forumid']) ? (int)$data['forum_forumid'] : "NULL";
	// 	$data['login_id'] = isset($data['login_id']) ? (int)$data['login_id'] : 0;
	// 	$data['forum_modid'] = isset($data['forum_modid']) ? (int)$data['forum_modid'] : 0;
	// 	$data['forum_display'] = isset($data['forum_display']) ? (int)$data['forum_display'] : 0;

	// 	if(isset($data['event_startdate'])){
	// 		$data['event_startdate'] = date('Y-m-d', strtotime($data['event_startdate']));
	// 	}

	// 	if(isset($data['forum_active']))
	// 		$data['forum_active'] = preg_match('/[YN]/', $data['forum_active']) ? $data['forum_active'] : Res::STATE_ACTIVE;
	// 	else
	// 		$data['forum_active'] = Res::STATE_ACTIVE;

	// 	if(isset($data['forum_closed']))
	// 		$data['forum_closed'] = preg_match('/[YN]/', $data['forum_closed']) ? $data['forum_closed'] : Forum::OPEN;
	// 	else
	// 		$data['forum_closed'] = Forum::OPEN;

	// 	if(isset($data['forum_allowchilds']))
	// 		$data['forum_allowchilds'] = preg_match('/[YN]/', $data['forum_allowchilds']) ? $data['forum_allowchilds'] : Forum::PROHIBIT_CHILDS;
	// 	else
	// 		$data['forum_allowchilds'] = Forum::PROHIBIT_CHILDS;

	// 	if(isset($data['forum_name']))
	// 		$data['forum_name'] = mb_strcut($data['forum_name'], 0, 128);
	// 	else
	// 		$data['forum_name'] = '';

	// 	if(!isset($data['forum_username']))
	// 		$data['forum_username'] = '';

	// 	if(!isset($data['forum_userlogin']))
	// 		$data['forum_userlogin'] = '';

	// 	if(!isset($data['forum_useremail']))
	// 		$data['forum_useremail'] = '';

	// 	if(!isset($data['forum_data']))
	// 		$data['forum_data'] = '';

	// 	if(!isset($data['forum_datacompiled']))
	// 		$data['forum_datacompiled'] = $data['forum_data'];

	// 	if(!isset($data['forum_entered']))
	// 		$data['forum_entered'] = '';

	// 	parse_text_data($data['forum_datacompiled']);

	// 	my_strip_tags($data['forum_name']);
	// 	my_strip_tags($data['forum_username']);
	// 	my_strip_tags($data['forum_useremail']);

	// }

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
	}

	// public function set_forum_path(Template $template, $forum_id)
	// {
	// 	if(!($tree = $this->get_tree($forum_id)))
	// 		return false;

	// 	$template->enable('BLOCK_forum_path');
	// 	foreach($tree as $key=>$item)
	// 	{
	// 		if(isset($tree[$key + 0]))
	// 		{
	// 			$forum_path = Forum::Route($item);
	// 			$template->set_var('forum_name', addslashes($item['forum_name']), 'BLOCK_forum_path');
	// 			$template->set_var('forum_path', $forum_path, 'BLOCK_forum_path');
	// 			$template->parse_block('BLOCK_forum_path', TMPL_APPEND);
	// 		}
	// 	}

	// 	return true;
	// }

	public static function hasNewThemes($item)
	{
		if(!User::logged()){
			return false;
		}

		// res_child_last_date
		if(isset($_SESSION['forums']['viewed_date'][$item['forum_id']]))
			return (strtotime($item['res_child_last_date']) > strtotime($_SESSION['forums']['viewed_date'][$item['forum_id']]));

		if(isset($_SESSION['forums']['viewed'][$item['forum_id']]))
			return ($item['forum_themecount'] > $_SESSION['forums']['viewed'][$item['forum_id']]);

		if(isset($_SESSION['res']['viewed_before']))
			return ($_SESSION['res']['viewed_before'] < strtotime($item['res_child_last_date']));

		return ($item['forum_themecount']??0 > 0);
	}

	public static function markThemeCount($item)
	{
		if(!User::logged()){
			return false;
		}
		$_SESSION['forums']['viewed_date'][$item['forum_id']] = $item['res_child_last_date'];
	}

	static function RouteFromRes(array $res, int $c_id = 0): string
	{
		return static::Route($res['forum_id']??$res['doc_id'], $res['res_name'], $c_id);
	}

	static function Route(int $forum_id, string $forum_name, int $c_id = 0): string
	{
		return "/forum/$forum_id-".urlize($forum_name).($c_id ? "#comment$c_id" : "");
	}

}
