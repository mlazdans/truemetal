<?php declare(strict_types = 1);

use dqdp\SQL\Select;

class ResForumFilter extends ResFilter
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

		# ForumFilter
		public ?int $forum_id              = null,
		public ?bool $actual_events        = null,
		public ?int $forum_allow_childs    = null,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['forum_id', 'type_id', 'forum_allow_childs']);

		if($this->actual_events)
		{
			$sql->Where(["type_id = ?", Forum::TYPE_EVENT]);
			$sql->Where(["event_startdate >= ?", date('Y-m-d')]);
		}

		return parent::apply_filter($sql);
	}

}
