<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ViewResGalleryCollection extends \dqdp\Collection {
	function current(): ViewResGalleryType {
		return parent::current();
	}

	function offsetGet(mixed $k): ViewResGalleryType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ViewResGalleryType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ViewResGalleryType but found: ".get_multitype($v));
		}
	}
}