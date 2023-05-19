<?php declare(strict_types = 1);

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ViewAttendFilter extends AbstractFilter
{
	function __construct(
		public ?int $res_id                = null,
		public ?int $l_id                  = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['res_id', 'l_id']);

		return $sql;
	}

}
