<?php declare(strict_types = 1);

class ViewResGdType extends AbstractViewResType {
	use ViewResGdTypeTrait;

	function route(): string
	{
		return GalleryData::RouteFromStr($this->gd_id);
	}
}
