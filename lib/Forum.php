<?php declare(strict_types = 1);

use dqdp\TODO;

class Forum
{
	const DISPLAY_DATACOMPILED = 0;
	const DISPLAY_DATA = 1;

	const SORT_THEME = 'T';
	const SORT_LASTCOMMENT = 'C';
	const SORT_DESC = 'D';
	const SORT_ASC = 'A';

	const TYPE_STD = 0;
	const TYPE_EVENT = 1;

	static array $types = [
		self::TYPE_STD=>'Forums',
		self::TYPE_EVENT=>'Pasākums',
	];

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
	// 	parse_text_data($data['forum_datacompiled']);
	// 	my_strip_tags($data['forum_name']);
	// 	my_strip_tags($data['forum_username']);
	// 	my_strip_tags($data['forum_useremail']);
	// }

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

	public static function hasNewComments(ViewResForumType $item)
	{
		return Res::hasNewComments($item->res_id, $item->res_comment_last_date);
	}

	public static function hasNewThemes(ViewResForumType $item)
	{
		if(!User::logged()){
			return false;
		}

		if(empty($item->res_child_last_date)){
			return false;
		}

		if(isset($_SESSION['forums']['viewed_date'][$item->forum_id])){
			return (strtotime($item->res_child_last_date) > strtotime($_SESSION['forums']['viewed_date'][$item->forum_id]));
		}

		if(isset($_SESSION['res']['viewed_before'])){
			return ($_SESSION['res']['viewed_before'] < strtotime($item->res_child_last_date));
		}

		// Šķiet šis 'viewed' ir kaut kāds vecs artifakts
		// if(isset($_SESSION['forums']['viewed'][$item->forum_id]))
		// 	return ($item->res_child_count > $_SESSION['forums']['viewed'][$item->forum_id]);

		return false;
	}

	static function RouteFromStr(int $forum_id, string $forum_name, ?int $c_id = null): string
	{
		return "/forum/$forum_id-".urlize($forum_name).($c_id ? "#comment$c_id" : "");
	}

	# TODO: izpētīt vai var apvienot ar $_SESSION['res']['viewed_date'][$res_id]
	static function markThemeCount(ViewResForumType $item)
	{
		if(!User::logged()){
			return false;
		}

		$_SESSION['forums']['viewed_date'][$item->forum_id] = date('Y-m-d H:i:s');
	}
}
