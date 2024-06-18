<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

class ResVoteCollection extends \dqdp\Collection
{
	function current(): ResVoteType {
		return parent::current();
	}

	function offsetGet(mixed $k): ResVoteType {
		return parent::offsetGet($k);
	}

	function offsetSet(mixed $k, mixed $v): void {
		if($v instanceof ResVoteType){
			parent::offsetSet($k, $v);
		} else {
			throw new \InvalidArgumentException("Expected ResVoteType but found: ".get_multitype($v));
		}
	}
}