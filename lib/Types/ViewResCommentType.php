<?php declare(strict_types = 1);

class ViewResCommentType implements ResourceTypeInterface {
	use ViewResCommentTypeTrait;

	function Route(int $c_id = null): ?string
	{
		if($parent = load_res($this->res_resid))
		{
			return $parent->Route($this->c_id);
		}

		return null;
	}

}
