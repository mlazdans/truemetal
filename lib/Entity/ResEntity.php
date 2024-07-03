<?php declare(strict_types = 1);

use dqdp\SQL\Update;

class ResEntity extends Entity
{
	use ResEntityTrait;

	static function show(int|array $res_id): bool
	{
		return static::set_visible($res_id, 1);
	}

	static function hide(int|array $res_id): bool
	{
		return static::set_visible($res_id, 0);
	}

	static function set_visible(int|array $res_id, int $visible): bool
	{
		if(!is_array($res_id)){
			$res_id = [$res_id];
		}

		assert($visible == 0 || $visible == 1);

		$O = (new static);

		$sql = (new Update($O->get_table_name()))->Set("res_visible = $visible" )->WhereIn("res_id", $res_id);

		return $O->get_trans()->query($sql) ? true : false;
	}

	static function move(int|array $res_id, int $res_resid): bool
	{
		if(!is_array($res_id)){
			$res_id = [$res_id];
		}

		$O = (new static);

		$sql = (new Update($O->get_table_name()))->Set("res_resid = $res_resid" )->WhereIn("res_id", $res_id);

		return $O->get_trans()->query($sql) ? true : false;
	}
}
