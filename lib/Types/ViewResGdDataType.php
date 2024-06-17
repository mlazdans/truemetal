<?php declare(strict_types = 1);

class ViewResGdDataType extends AbstractViewResType {
	use ViewResGdDataTypeTrait;

	function route(): string
	{
		return GalleryData::RouteFromStr($this->gd_id);
	}
}
