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

	static function RouteFromRes(array $res, int $c_id = 0): string
	{
		$parent = Res::GetAll($res['res_resid']);

		return Res::RouteFromRes($parent, $c_id);
	}
}
