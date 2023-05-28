<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ViewResForumCollection extends \dqdp\Collection {
	function current(): ViewResForumType {
		return parent::current();
	}

	function offsetGet(mixed $k): ViewResForumType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ViewResForumType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ViewResForumType but found: ".get_multitype($v));
		}
	}
}