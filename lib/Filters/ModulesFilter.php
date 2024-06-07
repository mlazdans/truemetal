<?php declare(strict_types = 1);

use dqdp\DBA\AbstractFilter;
use dqdp\DBA\Types\None;
use dqdp\SQL\Select;

class ModulesFilter extends AbstractFilter
{
	function __construct(
		public ?int $mod_id                    = null,
		public null|int|None $mod_modid        = null,
		public ?string $module_id              = null,
		public null|int|false $module_active   = 1,
		public null|int|false $module_visible  = false,
	) {}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_null_fields($sql, ['mod_modid']);
		$this->apply_set_fields($sql, ['mod_id', 'module_id']);
		$this->apply_falsed_fields($sql, ['module_active', 'module_visible']);

		$sql->OrderBy("mod_modid, module_pos");

		return $sql;
	}
}
