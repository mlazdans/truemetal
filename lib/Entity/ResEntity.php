<?php declare(strict_types = 1);

use dqdp\SQL\Update;

class ResEntity extends Entity
{
	use ResEntityTrait;

	function show(int|array $res_id)
	{
		return $this->set_visible($res_id, 1);
	}

	function hide(int|array $res_id)
	{
		return $this->set_visible($res_id, 0);
	}

	function set_visible(int|array $res_id, int $visible)
	{
		if(!is_array($res_id)){
			$res_id = [$res_id];
		}

		assert($visible == 0 || $visible == 1);

		$sql = (new Update($this->getTableName()))->Set("res_visible = $visible" )->WhereIn("res_id", $res_id);

		return $this->get_trans()->query($sql);
	}
}
