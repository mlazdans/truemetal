<?php declare(strict_types = 1);

class ViewResArticleType extends ViewResType implements ResourceTypeInterface {
	use ViewResArticleTypeTrait;

	function Route(): string
	{
		return Article::get_route($this->module_id, $this->art_id, $this->res_name);
	}
}
