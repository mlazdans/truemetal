<?php declare(strict_types = 1);

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ViewMainpageFilter extends AbstractFilter
{
	function __construct(
		public ?int $res_id                = null,
		public ?string $module_id          = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['res_id', 'module_id']);

		$this->orderBy("res_entered DESC");

		return $sql;
	}
}
