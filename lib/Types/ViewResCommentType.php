<?php declare(strict_types = 1);

class ViewResCommentType extends ViewResType implements ResourceTypeInterface {
	use ViewResCommentTypeTrait;

	function Route(): string
	{
		return $this->parent_res_route.'#comment'.$this->c_id;
	}
}
