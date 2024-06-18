<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ForumFilter extends AbstractFilter
{
	function __construct(public ?int $forum_id = null, public ?int $res_id = null, public ?int $forum_modid = null, public ?int $forum_allow_childs = null, public ?int $forum_closed = null, public ?int $forum_display = null, public ?int $type_id = null, public ?string $event_startdate = null) {
	}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['forum_id', 'res_id', 'forum_modid', 'forum_allow_childs', 'forum_closed', 'forum_display', 'type_id', 'event_startdate']);
		return $sql;
	}
}
