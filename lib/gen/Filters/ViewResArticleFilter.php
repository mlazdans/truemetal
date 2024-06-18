<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;
use dqdp\SQL\Select;

class ViewResArticleFilter extends AbstractFilter
{
	function __construct(public ?int $art_id = null, public ?int $art_modid = null, public ?int $mod_id = null, public ?int $mod_modid = null, public ?string $module_id = null, public ?string $module_name = null, public ?string $module_descr = null, public ?int $module_active = null, public ?int $module_visible = null, public ?int $module_pos = null, public ?string $module_data = null, public ?string $module_entered = null, public ?string $module_type = null, public ?int $res_id = null, public ?string $res_hash = null, public ?int $res_resid = null, public ?int $res_kind = null, public ?int $login_id = null, public ?string $res_entered = null, public ?string $res_nickname = null, public ?string $res_email = null, public ?string $res_ip = null, public ?int $res_visible = null, public ?string $res_name = null, public ?string $res_intro = null, public ?string $res_data = null, public ?string $res_data_compiled = null, public ?string $res_route = null, public ?int $res_votes = null, public ?int $res_votes_plus_count = null, public ?int $res_votes_minus_count = null, public ?int $res_child_count = null, public ?string $res_child_last_date = null, public ?int $res_comment_count = null, public ?string $res_comment_last_date = null, public ?string $l_hash = null) {
	}

	protected function apply_filter(Select $sql): Select
	{
		$this->apply_set_fields($sql, ['art_id', 'art_modid', 'mod_id', 'mod_modid', 'module_id', 'module_name', 'module_descr', 'module_active', 'module_visible', 'module_pos', 'module_data', 'module_entered', 'module_type', 'res_id', 'res_hash', 'res_resid', 'res_kind', 'login_id', 'res_entered', 'res_nickname', 'res_email', 'res_ip', 'res_visible', 'res_name', 'res_intro', 'res_data', 'res_data_compiled', 'res_route', 'res_votes', 'res_votes_plus_count', 'res_votes_minus_count', 'res_child_count', 'res_child_last_date', 'res_comment_count', 'res_comment_last_date', 'l_hash']);
		return $sql;
	}
}
