<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class JobCollection extends \dqdp\Collection {
	function current(): JobType {
		return parent::current();
	}

	function offsetGet(mixed $k): JobType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof JobType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected JobType but found: ".get_multitype($v));
		}
	}
}