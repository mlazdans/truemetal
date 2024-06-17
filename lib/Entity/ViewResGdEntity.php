<?php declare(strict_types = 1);

class ViewResGdEntity extends AbstractResEntity
{
	use ViewResGdEntityTrait;

	static function get_by_id(int $id, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdType
	{
		$F->gd_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->get_single($F);
	}

	static function get_by_res_id(int $res_id, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}

	static function get_by_hash(string $hash, bool $ignore_disabled = false, ?ResGdFilter $F = new ResGdFilter): ?ViewResGdType
	{
		$F->res_hash = $hash;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}
}
