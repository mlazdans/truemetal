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

// 	function insert(&$data, $validate = Res::ACT_VALIDATE)
// 	{
// 		global $ip;

// 		$this->login_id = 3;
// 		new TODO("get res_id");
// 		// if(!($res_id = parent::Add())) {
// 		// 	return false;
// 		// }

// 		if($validate)
// 			$this->validate($data);

// 		$date = DB::Now();
// 		if($data['art_entered'])
// 			$date = "'$data[art_entered]'";

// 		$data2 = DB::Quote($data);

// 		$sql = "
// INSERT INTO article (
// 	res_id, art_name, art_userip, art_entered,
// 	art_modid, art_data, art_intro, art_active,
// 	login_id
// ) VALUES (
// 	$res_id, '$data2[art_name]', '$ip', ".$date.",
// 	$data2[art_modid], '$data2[art_data]', '$data2[art_intro]', '$data2[art_active]',
// 	$this->login_id
// )";

// 		return (DB::Execute($sql) ? DB::LastID() : false);
// 	}

// 	function update($art_id, &$data, $validate = Res::ACT_VALIDATE)
// 	{
// 		$art_id = (integer)$art_id;
// 		if(!$art_id)
// 		{
// 			$this->error_msg = 'Nav norādīts vai nepareizs raksta ID<br>';
// 			return false;
// 		}

// 		if($validate)
// 			$this->validate($data);

// 		$data2 = DB::Quote($data);

// 		$sql = 'UPDATE article SET ';
// 		$sql .= $data2['art_name'] ? "art_name = '$data2[art_name]', " : '';
// 		$sql .= $data2['art_entered'] ? "art_entered = '$data2[art_entered]', " : '';
// 		$sql .= "art_active = '$data2[art_active]', ";
// 		$sql .= "art_data = '$data2[art_data]', ";
// 		$sql .= "art_intro = '$data2[art_intro]', ";
// 		if(!empty($data2['art_modid']))
// 			$sql .= "art_modid = $data2[art_modid], ";
// 		$sql = substr($sql, 0, -2);
// 		$sql .= ' WHERE art_id = '.$art_id;

// 		return (DB::Execute($sql) ? $art_id : false);
// 	}

// 	function save($art_id, &$data)
// 	{
// 		$this->validate($data);

// 		$art_id = (int)$art_id;
// 		$error_msg = '';

// 		if(!$data['art_modid'])
// 			$error_msg .= 'Nav norādīts vai nepareizs moduļa ID<br>';

// 		if(!$data['art_name'])
// 			$error_msg .= 'Nav norādīts ziņas nosaukums<br>';

// 		if(!$error_msg)
// 		{
// 			if($art_id)
// 				return $this->update($art_id, $data, Res::ACT_DONTVALIDATE);
// 			else
// 				return $this->insert($data, Res::ACT_DONTVALIDATE);
// 		} else { // $error_msg
// 			$this->error_msg = $error_msg;
// 			return false;
// 		}
// 	}

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
	// 	if(isset($data['art_modid']))
	// 		$data['art_modid'] = preg_match('/[0-9]/', $data['art_modid']) ? (int)$data['art_modid'] : 0;
	// 	else
	// 		$data['art_modid'] = 0;

	// 	if(isset($data['art_modid']))
	// 		$data['art_modid'] = preg_match('/[0-9]/', $data['art_modid']) ? (int)$data['art_modid'] : 0;
	// 	else
	// 		$data['art_modid'] = 0;

	// 	if(isset($data['art_active']))
	// 		$data['art_active'] = preg_match('/[YN]/', $data['art_active']) ? $data['art_active'] : Res::STATE_ACTIVE;
	// 	else
	// 		$data['art_active'] = Res::STATE_ACTIVE;

	// 	if(!isset($data['art_name']))
	// 		$data['art_name'] = '';

	// 	if(!isset($data['art_data']))
	// 		$data['art_data'] = '';

	// 	if(!isset($data['art_intro']))
	// 		$data['art_intro'] = '';

	// 	if(!isset($data['art_entered']))
	// 		$data['art_entered'] = '';

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

