<?php declare(strict_types = 1);

use dqdp\SQL\Select;

class ResArticleFilter extends ResFilter
{
	function __construct(
		# keep sync with ResFilter
		public ?int $res_id                = null,
		public null|int|false $res_resid   = null, // false: WHERE res_resid IS NULL
		public ?int $table_id              = null,
		public ?int $login_id              = null,
		public null|int|false $res_visible = 1,
		public ?array $ips                 = null,
		public ?array $res_ids             = null,

		# ArticleFilter
		public ?int $art_id              = null,
		public ?int $art_modid              = null,

		# Module filter
		public null|int|false $module_active = 1,

	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['art_id', 'art_modid']);

		return parent::apply_filter($sql);
	}

}
