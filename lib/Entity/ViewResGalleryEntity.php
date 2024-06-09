<?php declare(strict_types = 1);

class ViewResGalleryEntity extends AbstractResEntity
{
	use ViewResGalleryEntityTrait;

	static function get_by_id(int $id, bool $ignore_disabled = false, ?ResGalleryFilter $F = new ResGalleryFilter): ?ViewResGalleryType
	{
		$F->gal_id = $id;
		if($ignore_disabled){
			$F->res_visible = false;
		}

		return (new static)->get_single($F);
	}

	static function get_by_res_id(int $res_id, bool $ignore_disabled = false, ?ResGalleryFilter $F = new ResGalleryFilter): ?ViewResGalleryType
	{
		$F->res_id = $res_id;
		if($ignore_disabled)$F->res_visible = false;

		return (new static)->get_single($F);
	}
}
