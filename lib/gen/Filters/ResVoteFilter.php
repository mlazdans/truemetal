<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ResVoteFilter extends AbstractFilter
{
	function __construct(public ?int $res_id = null, public ?int $login_id = null, public ?int $rv_value = null, public ?string $rv_userip = null, public ?string $rv_entered = null) {
	}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['res_id', 'login_id', 'rv_value', 'rv_userip', 'rv_entered']);
		return $sql;
	}
}
