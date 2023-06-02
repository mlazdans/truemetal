<?php declare(strict_types = 1);

class ViewResArticleType extends ViewResType implements ResourceTypeInterface {
	use ViewResArticleTypeTrait;

	function Route(int $c_id = null): string
	{
		return Article::RouteFromStr($this->module_id, $this->art_id, $this->res_name, $c_id);
	}
}
