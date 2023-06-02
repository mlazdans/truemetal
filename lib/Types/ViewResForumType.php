<?php declare(strict_types = 1);

class ViewResForumType extends ViewResType implements ResourceTypeInterface {
	use ViewResForumTypeTrait;

	function Route(): string
	{
		return Forum::RouteFromStr($this->forum_id, $this->res_name);
	}
}
