<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

class ResRedirectCollection extends \dqdp\Collection
{
	function current(): ResRedirectType {
		return parent::current();
	}

	function offsetGet(mixed $k): ResRedirectType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ResRedirectType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ResRedirectType but found: ".get_multitype($v));
		}
	}
}