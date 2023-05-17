<?php declare(strict_types = 1);

use dqdp\DataObject;

class ViewResForumType extends DataObject implements ResourceTypeInterface {
	use ViewResForumTypeTrait;

	function Route(int $c_id = null): string
	{
		return Forum::RouteFromStr($this->forum_id, $this->res_name, $c_id);
	}
}
