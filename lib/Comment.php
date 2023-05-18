<?php declare(strict_types = 1);

class Comment extends AbstractRes
{
	protected ResCommentFilter $F;

	function __construct(ResCommentFilter $F = new ResCommentFilter)
	{
		$this->F = $F;
	}

	function load(): ViewResCommentCollection
	{
		return (new ViewResCommentEntity)->getAll($this->F);
	}

	static function load_by_id(int $c_id): ?ViewResCommentType
	{
		$F = new ResCommentFilter(c_id:$c_id);

		return (new static($F))->load_single();
	}

	static function load_by_res_id(int $res_id): ?ViewResCommentType
	{
		$F = new ResCommentFilter(res_id:$res_id);

		return (new static($F))->load_single();
	}
}
