<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class SearchLogCollection extends \dqdp\Collection {
	function current(): SearchLogType {
		return parent::current();
	}

	function offsetGet(mixed $k): SearchLogType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof SearchLogType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected SearchLogType but found: ".get_multitype($v));
		}
	}
}