<?php declare(strict_types = 1);

use dqdp\SQL\Select;
use dqdp\TODO;

class Comment implements ResourceInterface
{
	static function load(array $params)
	{
		$sql = (new Select("comment.*, res.*, res_meta.*"))
			->From('res')
			->Join('res_meta', 'res_meta.res_id = res.res_id')
			->Join('comment', 'comment.res_id = res.res_id')
		;

		join_logins($sql);

		if(isset($params['c_id'])){
			$sql->Where(["comment.c_id = ?", $params['c_id']]);
		}

		if(isset($params['res_id'])){
			$sql->Where(["comment.res_id = ?", $params['res_id']]);
		}

		if(isset($params['login_id'])){
			$sql->Where(["res.login_id = ?", $params['login_id']]);
		}

		if(isset($params['c_visible']))
		{
			new TODO('c_visible: use res_visible');
		}

		if(defaulted($params, 'res_visible'))
		{
			$sql->Where("res.res_visible = 1");
		} elseif(!ignored($params, 'res_visible')){
			$sql->Where(["res.res_visible = ?", $params['res_visible']]);
		}

		if(isset($params['sort'])){
			new TODO('sort: use order');
		}

		if(empty($params['order'])){
			$sql->OrderBy("res.res_entered DESC");
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

		return (isset($params['c_id']) || isset($params['res_id']) ? DB::ExecuteSingle($sql) : DB::Execute($sql));
	}

	function Add()
	{
		list($data) = func_get_args();

		$this->login_id = $data['login_id'];
		new TODO("Get res_id");
		// if(!($res_id = parent::Add())) {
		// 	return false;
		// }

		$this->validate($data);
		$data = DB::Quote($data);

		$sql = "
INSERT INTO comment (
	res_id, login_id, c_userlogin, c_username,
	c_useremail, c_data, c_datacompiled,
	c_visible, c_userip, c_entered
) VALUES (
	$res_id, $data[login_id], '$data[c_userlogin]', '$data[c_username]',
	'$data[c_useremail]', '$data[c_data]', '$data[c_datacompiled]',
	'$data[c_visible]', '$data[c_userip]', ".DB::now()."
)
";

		return DB::Execute($sql);
	}


	function Get2(Array $params = array())
	{
// 		$sql = "
// SELECT
// 	comment.*,
// 	r.res_votes
// FROM comment
// JOIN res r ON r.res_id = comment.res_id
// ";

// 		$sql_add = array();

		// if(!empty($params['c_id']))
		// 	$sql_add[] = sprintf("(c_id = %d)", $params['c_id']);

		// if(isset($params['res_id']))
		// 	$sql_add[] = sprintf("comment.res_id = %d", $params['res_id']);

		// if(isset($params['c_visible']))
		// {
		// 	if($params['c_visible'])
		// 		$sql_add[] = sprintf("c_visible = '%s'", $params['c_visible']);
		// } else {
		// 	$sql_add[] = sprintf("c_visible = '%s'", Res::STATE_VISIBLE);
		// }

		// if($sql_add)
		// 	$sql .= " WHERE ".join(' AND ', $sql_add);

		// if(empty($params['sort']))
		// 	$sql .= " ORDER BY c_entered ";
		// else
		// 	$sql .= " ORDER BY $params[sort] ";

		// if(!empty($params['limit']))
		// 	$sql .= " LIMIT ".$params['limit'];

		// return (isset($params['c_id']) || isset($params['res_id']) ? DB::ExecuteSingle($sql) : DB::Execute($sql));
	}

	function Delete($id)
	{
		$sql = sprintf("DELETE FROM `comment` WHERE c_id = %d", $id);

		return DB::Execute($sql);
	}

	function Show($id)
	{
		$sql = sprintf(
			"UPDATE `comment` SET c_visible = '%s' WHERE c_id = %d",
			Res::STATE_VISIBLE,
			$id
			);

		return DB::Execute($sql);
	}

	function Hide($id)
	{
		$sql = sprintf(
			"UPDATE `comment` SET c_visible = '%s' WHERE c_id = %d",
			Res::STATE_INVISIBLE,
			$id
			);

		return DB::Execute($sql);
	}

	function Validate(&$data)
	{
		if(!isset($data['c_username']))
			$data['c_username'] = '';

		if(!isset($data['c_useremail']))
			$data['c_useremail'] = '';

		if(!isset($data['c_data']))
			$data['c_data'] = '';

		$data['c_datacompiled'] = $data['c_data'];
		parse_text_data($data['c_datacompiled']);

		if(isset($data['c_visible']))
			$data['c_visible'] = ereg('[^YN]', $data['c_visible']) ? '' : $data['c_visible'];
		else
			$data['c_visible'] = Res::STATE_VISIBLE;

	}

	static function RouteFromRes(array $res, int $c_id = 0): string
	{
		new TODO("Route");
		// return static::Route($res['forum_id'], $res['res_name'], $c_id);
	}

	static function Route(int $forum_id, string $forum_name, int $c_id = 0): string
	{
		new TODO("Route");
		// return "/forum/$forum_id-".urlize($forum_name).($c_id ? "#comment$c_id" : "");
	}

}
