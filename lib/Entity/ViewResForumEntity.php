<?php declare(strict_types = 1);

class ViewResForumEntity extends AbstractResEntity
{
	use ViewResForumEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResForumFilter $F = new ResForumFilter): ?ViewResForumType
	{
		$F->forum_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResForumFilter $F = new ResForumFilter): ?ViewResForumType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->getSingle($F);
	}
}
