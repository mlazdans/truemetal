<?php declare(strict_types = 1);

class ViewResForumType implements ResourceTypeInterface {
	use ViewResForumTypeTrait;

	function Route(int $c_id = null): string
	{
		return Forum::RouteFromStr($this->forum_id, $this->res_name, $c_id);
	}
}
