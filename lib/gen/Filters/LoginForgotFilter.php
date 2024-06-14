<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class LoginForgotFilter extends AbstractFilter
{
	function __construct(public ?int $f_id = null) {
	}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['f_id']);
		return $sql;
	}
}