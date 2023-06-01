<?php declare(strict_types = 1);

class ViewResGDDataEntity extends AbstractResEntity
{
	use ViewResGdDataEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResGDFilter $F = new ResGDFilter): ?ViewResGDDataType
	{
		$F->gd_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResGDFilter $F = new ResGDFilter): ?ViewResGDDataType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->getSingle($F);
	}
}