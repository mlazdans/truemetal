<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ArticleCollection extends \dqdp\Collection {
	function current(): ArticleType {
		return parent::current();
	}

	function offsetGet(mixed $k): ArticleType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ArticleType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ArticleType but found: ".get_multitype($v));
		}
	}
}