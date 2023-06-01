<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ViewResOrphansCollection extends \dqdp\Collection {
	function current(): ViewResOrphansType {
		return parent::current();
	}

	function offsetGet(mixed $k): ViewResOrphansType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ViewResOrphansType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ViewResOrphansType but found: ".get_multitype($v));
		}
	}
}