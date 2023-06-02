<?php declare(strict_types = 1);

class ViewResGdEntity extends AbstractResEntity
{
	use ViewResGdEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdType
	{
		$F->gd_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->getSingle($F);
	}
}
