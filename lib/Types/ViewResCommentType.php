<?php declare(strict_types = 1);

use dqdp\DataObject;

class ViewResCommentType extends DataObject implements ResourceTypeInterface {
	use ViewResCommentTypeTrait;

	function Route(int $c_id = null): string
	{
		$parent = Res::GetAll($this->res_resid);

		return $parent->Route($this->c_id);
	}

}
