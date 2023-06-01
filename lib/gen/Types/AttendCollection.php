<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class AttendCollection extends \dqdp\Collection {
	function current(): AttendType {
		return parent::current();
	}

	function offsetGet(mixed $k): AttendType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof AttendType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected AttendType but found: ".get_multitype($v));
		}
	}
}