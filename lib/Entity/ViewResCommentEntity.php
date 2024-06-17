<?php declare(strict_types = 1);

class ViewResCommentEntity extends AbstractResEntity
{
	use ViewResCommentEntityTrait;

	static function get_by_id(int $id, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter): ?ViewResCommentType
	{
		$F->c_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->get_single($F);
	}

	static function get_by_res_id(int $res_id, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter): ?ViewResCommentType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}

	static function get_by_hash(string $hash, bool $ignore_disabled = false, ?ResCommentFilter $F = new ResCommentFilter): ?ViewResCommentType
	{
		$F->res_hash = $hash;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}
}
