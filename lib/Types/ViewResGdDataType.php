<?php declare(strict_types = 1);

class ViewResGdDataType extends ViewResType implements ResourceTypeInterface {
	use ViewResGdDataTypeTrait;

	function Route(): string
	{
		return GalleryData::RouteFromStr($this->gd_id);
	}
}
