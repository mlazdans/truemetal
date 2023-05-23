<?php declare(strict_types = 1);

use dqdp\SQL\Select;

class ResGalleryFilter extends ResFilter
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
		// Gallery
		public ?int $gal_id                = null,
		public ?int $gal_ggid              = null,
		public ?int $gg_id                 = null,
		// public ?string $gg_name            = null,
		// public ?string $gg_date            = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['gal_id', 'gal_ggid', 'gg_id']);

		return parent::apply_filter($sql);
	}

}
