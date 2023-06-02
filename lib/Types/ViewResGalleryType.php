<?php declare(strict_types = 1);

class ViewResGalleryType extends ViewResType implements ResourceTypeInterface {
	use ViewResGalleryTypeTrait;

	function Route(): string
	{
		return Gallery::RouteFromStr($this->gal_id);
	}
}
