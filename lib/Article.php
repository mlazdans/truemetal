<?php declare(strict_types = 1);

class Article
{
	public static function has_new_comments(ViewResArticleType $item)
	{
		return Res::not_seen($item->res_id, $item->res_comment_last_date??$item->res_entered);
	}

	function del($art_id)
	{
		if(!$art_id)
		{
			return true;
		}

		$sql = 'DELETE FROM `article` WHERE art_id = '.$art_id;

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

		if(isset($data['article_count']) && $func)
		{
			for($r = 1; $r <= $data['article_count']; ++$r)
			{
				// ja iechekots, proceseejam
				if(isset($data['art_checked'.$r]) && isset($data['art_id'.$r]))
				{
					$ret = $ret && $this->{$func}($data['art_id'.$r]);
				}
			}
		}

		return $ret;
	}

	function get_total($art_modid = 0)
	{
		$sql_add = '';
		$sql = "SELECT COUNT(*) art_count FROM `article` a";
		if($art_modid)
			$sql_add .= "a.art_modid = $art_modid AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= " WHERE $sql_add";

		$data = DB::ExecuteSingle($sql);

		return $data['art_count'];
	}

	static function RouteFromStr(string $module_id, int $art_id, string $res_name): string
	{
		return "/$module_id/$art_id-".urlize($res_name);
	}
}

