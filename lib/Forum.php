<?php declare(strict_types = 1);

use dqdp\TODO;

class Forum
{
	const DISPLAY_DATACOMPILED = 0;
	const DISPLAY_DATA         = 1;

	const SORT_THEME           = 0;
	const SORT_LASTCOMMENT     = 1;

	const SORT_ASC             = 0;
	const SORT_DESC            = 1;

	const TYPE_STD             = 0;
	const TYPE_EVENT           = 1;

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

	public static function has_new_comments(ViewResForumType $item)
	{
		return Res::is_marked_since($item->res_id, $item->res_comment_last_date);
	}

	public static function has_new_themes(ViewResForumType $item)
	{
		return Res::is_marked_since($item->res_id, $item->res_child_last_date);
	}

	static function RouteFromStr(int $forum_id, string $forum_name): string
	{
		return "/forum/$forum_id-".urlize($forum_name);
	}
}
