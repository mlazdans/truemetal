<?php declare(strict_types = 1);

class ViewResCommentEntity extends AbstractResEntity
{
	use ViewResCommentEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter): ?ViewResCommentType
	{
		$F->c_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter): ?ViewResCommentType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->getSingle($F);
	}

}
