<?php declare(strict_types = 1);

use dqdp\SQL\Select;

class ResGDFilter extends ResFilter
{
	function __construct(
		// NOTE                            : keep sync with ResFilter
		public ?int $res_id                = null,
		public null|int|false $res_resid   = null, // false: WHERE res_resid IS NULL
		public ?int $table_id              = null,
		public ?int $login_id              = null,
		public null|int|false $res_visible = 1,
		public ?array $ips                 = null,
		public ?array $res_ids             = null,
		// Gallery Data
		public ?int $gd_id                 = null,
		public ?int $gal_id                = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['gd_id', 'gal_id']);

		return parent::apply_filter($sql);
	}

}
