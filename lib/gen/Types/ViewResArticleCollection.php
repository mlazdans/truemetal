<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!


class ViewResArticleCollection extends \dqdp\Collection {
	function current(): ViewResArticleType {
		return parent::current();
	}

	function offsetGet(mixed $k): ViewResArticleType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ViewResArticleType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ViewResArticleType but found: ".get_multitype($v));
		}
	}
}