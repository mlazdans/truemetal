<?php declare(strict_types = 1);

class ViewResForumEntity extends AbstractResEntity
{
	use ViewResForumEntityTrait;

	static function get_by_id(int $id, bool $ignore_disabled = false, ?ResForumFilter $F = new ResForumFilter): ?ViewResForumType
	{
		$F->forum_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->get_single($F);
	}

	static function get_by_res_id(int $res_id, bool $ignore_disabled = false, ?ResForumFilter $F = new ResForumFilter): ?ViewResForumType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}

	static function get_by_hash(string $hash, bool $ignore_disabled = false, ?ResForumFilter $F = new ResForumFilter): ?ViewResForumType
	{
		$F->res_hash = $hash;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}
}
