<?php declare(strict_types = 1);

class ViewResEntity extends AbstractResEntity
{
	use ViewResEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResFilter $F = new ResFilter)
	{
		$F->res_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResFilter $F = new ResFilter)
	{
		return static::getById($res_id, $ignore_disabled, $F);
	}

}
