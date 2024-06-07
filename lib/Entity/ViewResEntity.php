<?php declare(strict_types = 1);

class ViewResEntity extends AbstractResEntity
{
	use ViewResEntityTrait;

	static function get_by_id(int $id, bool $ignore_disabled = false, ?ResFilter $F = new ResFilter): ?ViewResType
	{
		$F->res_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->get_single($F);
	}

	static function get_by_res_id(int $res_id, bool $ignore_disabled = false, ?ResFilter $F = new ResFilter): ?ViewResType
	{
		return static::get_by_id($res_id, $ignore_disabled, $F);
	}

}
