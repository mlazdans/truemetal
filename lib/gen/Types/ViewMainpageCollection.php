<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ViewMainpageCollection extends \dqdp\Collection {
	function current(): ViewMainpageType {
		return parent::current();
	}

	function offsetGet(mixed $k): ViewMainpageType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ViewMainpageType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ViewMainpageType but found: ".get_multitype($v));
		}
	}
}