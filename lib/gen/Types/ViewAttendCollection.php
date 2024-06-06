<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

class ViewAttendCollection extends \dqdp\Collection
{
	function current(): ViewAttendType {
		return parent::current();
	}

	function offsetGet(mixed $k): ViewAttendType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ViewAttendType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ViewAttendType but found: ".get_multitype($v));
		}
	}
}
