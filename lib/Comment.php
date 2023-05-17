<?php declare(strict_types = 1);

class Comment
{
	static function load(ResCommentFilter $F): ViewResCommentsCollection
	{
		return (new ViewResCommentsEntity)->getAll($F);
	}

	# TODO: abstract between all res classess
	static function load_single(ResCommentFilter $F): ?ViewResCommentsType
	{
		$data = Comment::load($F);

		assert($data->count() <= 1);

		if($data->count())
		{
			return $data[0];
		}

		return null;
	}

	static function load_by_id(int $c_id): ?ViewResCommentsType
	{
		return Comment::load_single(new ResCommentFilter(c_id: $c_id));
	}

	static function load_by_res_id(int $res_id): ?ViewResCommentsType
	{
		return Comment::load_single(new ResCommentFilter(res_id: $res_id));
	}
}
