<?php declare(strict_types = 1);

class ViewResGdType extends ViewResType implements ResourceTypeInterface {
	use ViewResGdTypeTrait;

	function Route(): string
	{
		return GalleryData::RouteFromStr($this->gd_id);
	}
}
