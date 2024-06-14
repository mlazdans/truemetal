<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ViewResGalleryFilter extends AbstractFilter
{
	function __construct(public ?int $gal_id = null) {
	}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['gal_id']);
		return $sql;
	}
}