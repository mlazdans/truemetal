<?php declare(strict_types = 1);

use dqdp\SQL\Select;
use dqdp\TODO;

class Article implements ResourceInterface
{
	// var $date_format;
	var $limit;
	var $error_msg;
	var $order;

	// protected $table_id = Table::ARTICLE;

	// function set_date_format($new_date)
	// {
	// 	$this->date_format = $new_date;
	// }

	static function load(array $params)
	{
		$sql = (new Select('article.*, modules.*, res.*, res_meta.*'))
			->From('res')
			->Join('res_meta', 'res_meta.res_id = res.res_id')
			->Join('article', 'article.res_id = res.res_id')
			->Join('modules', 'article.art_modid = modules.mod_id')
		;

		join_logins($sql);

		# TODO: abstract out
		$fields = ['art_id', 'res_id', 'art_modid'];
		foreach($fields as $f){
			if(isset($params[$f])){
				$sql->Where(["article.$f = ?", $params[$f]]);
			}
		}

		if(isset($params['res_ids'])){
			$sql->WhereIn("article.res_id", $params['res_id']);
		}

		if(defaulted($params, 'art_active'))
		{
			$sql->Where("res.res_visible = 1");
		} elseif(!ignored($params, 'art_active')){
			$sql->Where(["res.res_visible = ?", $params['art_active']]);
		}

		if(empty($params['order'])){
			$sql->OrderBy("res_entered DESC");
		} else {
			$sql->OrderBy($params['order']);
		}

		if(isset($params['rows']))
		{
			$sql->Rows((int)$params['rows']);
		}

		if(isset($params['limit']))
		{
			new TODO("Nodalīt rows un offset");
			// $sql .= " LIMIT $params[limit]";
		}

		return (isset($params['art_id']) || isset($params['res_id']) ? DB::ExecuteSingle($sql) : DB::Execute($sql));
	}

	function load2(Array $params = array())
	{
		// $sql_add = array();

		// if(isset($params['art_id']))
		// 	$sql_add[] = sprintf("art_id = %d", $params['art_id']);

		// if(isset($params['res_id']))
		// 	$sql_add[] = sprintf("a.res_id = %d", $params['res_id']);

		// if(isset($params['art_ids']) && is_array($params['art_ids']))
		// 	$sql_add[] = sprintf("art_id IN (%s)", join(",", $params['art_ids']));

		// if(isset($params['res_ids']) && is_array($params['res_ids']))
		// 	$sql_add[] = sprintf("a.res_id IN (%s)", join(",", $params['res_ids']));

		// if(isset($params['art_active']))
		// {
		// 	if($params['art_active'])
		// 		$sql_add[] = sprintf("art_active = '%s'", $params['art_active']);
		// } else {
		// 	$sql_add[] = sprintf("art_active = '%s'", Res::STATE_ACTIVE);
		// }

		// if(isset($params['art_modid']))
		// 	$sql_add[] = sprintf("art_modid = %d", $params['art_modid']);

		// if(isset($params['end_date']))
		// 	$sql_add[] = sprintf("art_entered <= '%s'", $params['end_date']);

// 		$sql = "
// SELECT
// 	a.*,
// 	m.*,
// 	r.*,
// 	COALESCE(res_comment_count, 0) AS res_comment_count,
// 	res_comment_lastdate
// FROM
// 	`article` a
// JOIN `modules` m ON (a.art_modid = m.mod_id)
// JOIN `res` r ON r.`res_id` = a.`res_id`
// ";

// 		if($sql_add)
// 			$sql .= " WHERE ".join(" AND ", $sql_add);

		// $sql .= (empty($params['order']) ? " ORDER BY art_entered DESC " : " ORDER BY $params[order] ");

		// if(isset($params['limit']))
		// 	$sql .= " LIMIT $params[limit]";

		// return (isset($params['art_id']) || isset($params['res_id']) ? DB::ExecuteSingle($sql) : DB::Execute($sql));
	} // load

	function insert(&$data, $validate = Res::ACT_VALIDATE)
	{
		global $ip;

		$this->login_id = 3;
		new TODO("get res_id");
		// if(!($res_id = parent::Add())) {
		// 	return false;
		// }

		if($validate)
			$this->validate($data);

		$date = DB::Now();
		if($data['art_entered'])
			$date = "'$data[art_entered]'";

		$data2 = DB::Quote($data);

		$sql = "
INSERT INTO article (
	res_id, art_name, art_userip, art_entered,
	art_modid, art_data, art_intro, art_active,
	login_id
) VALUES (
	$res_id, '$data2[art_name]', '$ip', ".$date.",
	$data2[art_modid], '$data2[art_data]', '$data2[art_intro]', '$data2[art_active]',
	$this->login_id
)";

		return (DB::Execute($sql) ? DB::LastID() : false);
	}

	function update($art_id, &$data, $validate = Res::ACT_VALIDATE)
	{
		$art_id = (integer)$art_id;
		if(!$art_id)
		{
			$this->error_msg = 'Nav norādīts vai nepareizs raksta ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$data2 = DB::Quote($data);

		$sql = 'UPDATE article SET ';
		$sql .= $data2['art_name'] ? "art_name = '$data2[art_name]', " : '';
		$sql .= $data2['art_entered'] ? "art_entered = '$data2[art_entered]', " : '';
		$sql .= "art_active = '$data2[art_active]', ";
		$sql .= "art_data = '$data2[art_data]', ";
		$sql .= "art_intro = '$data2[art_intro]', ";
		if(!empty($data2['art_modid']))
			$sql .= "art_modid = $data2[art_modid], ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE art_id = '.$art_id;

		return (DB::Execute($sql) ? $art_id : false);
	}

	function save($art_id, &$data)
	{
		$this->validate($data);

		$art_id = (int)$art_id;
		$error_msg = '';

		if(!$data['art_modid'])
			$error_msg .= 'Nav norādīts vai nepareizs moduļa ID<br>';

		if(!$data['art_name'])
			$error_msg .= 'Nav norādīts ziņas nosaukums<br>';

		if(!$error_msg)
		{
			if($art_id)
				return $this->update($art_id, $data, Res::ACT_DONTVALIDATE);
			else
				return $this->insert($data, Res::ACT_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
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

	function activate($art_id)
	{
		$art_id = (integer)$art_id;
		$sql = 'UPDATE `article` SET art_active = "'.Res::STATE_ACTIVE.'" WHERE art_id = '.$art_id;

		return DB::Execute($sql);
	}

	function deactivate($art_id)
	{
		$art_id = (integer)$art_id;
		$sql = 'UPDATE `article` SET art_active = "'.Res::STATE_INACTIVE.'" WHERE art_id = '.$art_id;

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

	function validate(&$data)
	{
		if(isset($data['art_modid']))
			$data['art_modid'] = preg_match('/[0-9]/', $data['art_modid']) ? (int)$data['art_modid'] : 0;
		else
			$data['art_modid'] = 0;

		if(isset($data['art_modid']))
			$data['art_modid'] = preg_match('/[0-9]/', $data['art_modid']) ? (int)$data['art_modid'] : 0;
		else
			$data['art_modid'] = 0;

		if(isset($data['art_active']))
			$data['art_active'] = preg_match('/[YN]/', $data['art_active']) ? $data['art_active'] : Res::STATE_ACTIVE;
		else
			$data['art_active'] = Res::STATE_ACTIVE;

		if(!isset($data['art_name']))
			$data['art_name'] = '';

		if(!isset($data['art_data']))
			$data['art_data'] = '';

		if(!isset($data['art_intro']))
			$data['art_intro'] = '';

		if(!isset($data['art_entered']))
			$data['art_entered'] = '';

		my_strip_tags($data['art_name']);
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

	static function RouteFromRes(array $res, int $c_id = 0): string
	{
		return static::Route($res['module_id'], $res['art_id']??$res['doc_id'], $res['res_name'], $c_id);
	}

	static function Route(string $module_id, int $art_id, string $res_name, int $c_id = 0): string
	{
		return "/$module_id/$art_id-".urlize($res_name).($c_id ? "#comment$c_id" : "");
	}
}

