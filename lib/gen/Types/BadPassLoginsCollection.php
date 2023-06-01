<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class BadPassLoginsCollection extends \dqdp\Collection {
	function current(): BadPassLoginsType {
		return parent::current();
	}

	function offsetGet(mixed $k): BadPassLoginsType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof BadPassLoginsType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected BadPassLoginsType but found: ".get_multitype($v));
		}
	}
}