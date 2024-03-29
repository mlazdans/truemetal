<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ResMetaCollection extends \dqdp\Collection {
	function current(): ResMetaType {
		return parent::current();
	}

	function offsetGet(mixed $k): ResMetaType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ResMetaType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ResMetaType but found: ".get_multitype($v));
		}
	}
}