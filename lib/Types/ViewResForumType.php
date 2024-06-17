<?php declare(strict_types = 1);

class ViewResForumType extends AbstractViewResType {
	use ViewResForumTypeTrait;

	function route(): string
	{
		return Forum::RouteFromStr($this->forum_id, $this->res_name);
	}
}
