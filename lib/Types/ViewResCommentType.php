<?php declare(strict_types = 1);

class ViewResCommentType extends AbstractViewResType {
	use ViewResCommentTypeTrait;

	function route(): string
	{
		return $this->parent_res_route.'#comment'.$this->c_id;
	}
}
