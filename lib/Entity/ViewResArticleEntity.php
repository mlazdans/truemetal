<?php declare(strict_types = 1);

class ViewResArticleEntity extends AbstractResEntity
{
	use ViewResArticleEntityTrait;

	static function getById(int $id, bool $ignore_disabled = false, ?ResArticleFilter $F = new ResArticleFilter): ?ViewResArticleType
	{
		$F->art_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->getSingle($F);
	}

	static function getByResId(int $res_id, bool $ignore_disabled = false, ?ResArticleFilter $F = new ResArticleFilter): ?ViewResArticleType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->getSingle($F);
	}

}
