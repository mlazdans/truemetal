<?php declare(strict_types = 1);

class Article extends AbstractRes
{
	protected ResArticleFilter $F;

	function __construct(ResArticleFilter $F = new ResArticleFilter)
	{
		$this->F = $F;
	}

	function load(): ViewResArticleCollection
	{
		return (new ViewResArticleEntity)->getAll($this->F);
	}

	static function load_by_id(int $art_id): ?ViewResArticleType
	{
		$F = new ResArticleFilter(art_id:$art_id);

		return (new static($F))->load_single();
	}

	static function load_by_res_id(int $res_id): ?ViewResArticleType
	{
		$F = new ResArticleFilter(res_id:$res_id);

		return (new static($F))->load_single();
	}

	public static function hasNewComments(ViewResArticleType $item)
	{
		return Res::hasNewComments($item->res_id, $item->res_comment_last_date, $item->res_child_count);
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

	// function validate(&$data)
	// {
	// 	my_strip_tags($data['art_name']);
	// }

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

	static function RouteFromStr(string $module_id, int $art_id, string $res_name, ?int $c_id = null): string
	{
		return "/$module_id/$art_id-".urlize($res_name).($c_id ? "#comment$c_id" : "");
	}
}

