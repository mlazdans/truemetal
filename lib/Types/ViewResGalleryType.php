<?php declare(strict_types = 1);

class ViewResGalleryType extends AbstractViewResType {
	use ViewResGalleryTypeTrait;

	function route(): string
	{
		return Gallery::RouteFromStr($this->gal_id);
	}
}
