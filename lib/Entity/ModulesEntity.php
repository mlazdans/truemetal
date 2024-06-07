<?php declare(strict_types = 1);

use dqdp\DBA\Types\None;

class ModulesEntity extends Entity
{
	use ModulesEntityTrait;

	static function get_by_module_id(string $module_id, null|int|None $mod_modid = new None, ?ModulesFilter $F = new ModulesFilter): ?ModulesType
	{
		$F->module_id = $module_id;
		$F->mod_modid = $mod_modid;

		return (new static)->get_single($F);
	}

}
