<?php declare(strict_types = 1);

use dqdp\SQL\Select;

class ResCommentFilter extends ResFilter
{
	function __construct(
		# keep sync with ResFilter
		public ?int $res_id                = null,
		public ?string $res_hash           = null,
		public null|int|false $res_resid   = null, // false: WHERE res_resid IS NULL
		public ?int $res_kind              = null,
		public ?int $login_id              = null,
		public null|int|false $res_visible = 1,
		public ?array $ips                 = null,
		public ?array $res_ids             = null,

		# CommentFilter
		public ?int $c_id                  = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['c_id']);

		return parent::apply_filter($sql);
	}

}
