<?php declare(strict_types = 1);

use dqdp\Collection;
use dqdp\DataObject;

abstract class AbstractRes
{
	abstract function load(): Collection;
	abstract static function load_by_id(int $id): ?DataObject;
	abstract static function load_by_res_id(int $res_id): ?DataObject;
	// abstract function get_filter(): ResFilter;

	protected function load_single(): ?DataObject
	{
		$data = $this->load();

		assert($data->count() <= 1);

		if($data->count())
		{
			return $data[0];
		}

		return null;
	}

	// protected function _load_by_res_id(int $res_id): ?DataObject
	// {
	// 	$this->get_filter()->res_id = $res_id;

	// 	return $this->load_single();
	// }
}
