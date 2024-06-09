<?php declare(strict_types = 1);

class ViewResGdDataEntity extends AbstractResEntity
{
	use ViewResGdDataEntityTrait;

	static function get_by_id(int $id, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdDataType
	{
		$F->gd_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->get_single($F);
	}

	static function get_by_res_id(int $res_id, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdDataType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}
}
