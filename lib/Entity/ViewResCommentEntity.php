<?php declare(strict_types = 1);

class ViewResCommentEntity extends AbstractResEntity
{
	use ViewResCommentEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter)
	{
		$F->c_id = $id;
		printr($F);
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter)
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->getSingle($F);
	}

}
