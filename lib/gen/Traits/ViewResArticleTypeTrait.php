<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait ViewResArticleTypeTrait {
	use PropertyInitTrait;

	var int $art_id;
	var int $art_modid;
	var int $mod_id;
	var ?int $mod_modid;
	var string $module_id;
	var string $module_name;
	var string $module_descr;
	var int $module_active;
	var int $module_visible;
	var int $module_pos;
	var string $module_data;
	var string $module_entered;
	var string $module_type;
	var int $res_id;
	var ?int $res_resid;
	var int $table_id;
	var ?int $login_id;
	var ?string $res_entered;
	var ?string $res_nickname;
	var ?string $res_email;
	var ?string $res_ip;
	var int $res_visible;
	var ?string $res_name;
	var ?string $res_intro;
	var ?string $res_data;
	var ?string $res_data_compiled;
	var ?int $res_votes;
	var ?int $res_votes_plus_count;
	var ?int $res_votes_minus_count;
	var ?int $res_child_count;
	var ?string $res_child_last_date;
	var ?int $res_comment_count;
	var ?string $res_comment_last_date;
	var ?string $l_hash;

	function __construct(?int $art_id = null, ?int $art_modid = null, ?int $mod_id = null, ?int $mod_modid = null, ?string $module_id = null, ?string $module_name = null, ?string $module_descr = null, ?int $module_active = null, ?int $module_visible = null, ?int $module_pos = null, ?string $module_data = null, ?string $module_entered = null, ?string $module_type = null, ?int $res_id = null, ?int $res_resid = null, ?int $table_id = null, ?int $login_id = null, ?string $res_entered = null, ?string $res_nickname = null, ?string $res_email = null, ?string $res_ip = null, ?int $res_visible = null, ?string $res_name = null, ?string $res_intro = null, ?string $res_data = null, ?string $res_data_compiled = null, ?int $res_votes = null, ?int $res_votes_plus_count = null, ?int $res_votes_minus_count = null, ?int $res_child_count = null, ?string $res_child_last_date = null, ?int $res_comment_count = null, ?string $res_comment_last_date = null, ?string $l_hash = null) {
		if(isset($art_id))$this->art_id = $art_id;
		if(isset($art_modid))$this->art_modid = $art_modid;
		if(isset($mod_id))$this->mod_id = $mod_id;
		if(isset($mod_modid))$this->mod_modid = $mod_modid;
		if(isset($module_id))$this->module_id = $module_id;
		if(isset($module_name))$this->module_name = $module_name;
		if(isset($module_descr))$this->module_descr = $module_descr;
		if(isset($module_active))$this->module_active = $module_active;
		if(isset($module_visible))$this->module_visible = $module_visible;
		if(isset($module_pos))$this->module_pos = $module_pos;
		if(isset($module_data))$this->module_data = $module_data;
		if(isset($module_entered))$this->module_entered = $module_entered;
		if(isset($module_type))$this->module_type = $module_type;
		if(isset($res_id))$this->res_id = $res_id;
		if(isset($res_resid))$this->res_resid = $res_resid;
		if(isset($table_id))$this->table_id = $table_id;
		if(isset($login_id))$this->login_id = $login_id;
		if(isset($res_entered))$this->res_entered = $res_entered;
		if(isset($res_nickname))$this->res_nickname = $res_nickname;
		if(isset($res_email))$this->res_email = $res_email;
		if(isset($res_ip))$this->res_ip = $res_ip;
		if(isset($res_visible))$this->res_visible = $res_visible;
		if(isset($res_name))$this->res_name = $res_name;
		if(isset($res_intro))$this->res_intro = $res_intro;
		if(isset($res_data))$this->res_data = $res_data;
		if(isset($res_data_compiled))$this->res_data_compiled = $res_data_compiled;
		if(isset($res_votes))$this->res_votes = $res_votes;
		if(isset($res_votes_plus_count))$this->res_votes_plus_count = $res_votes_plus_count;
		if(isset($res_votes_minus_count))$this->res_votes_minus_count = $res_votes_minus_count;
		if(isset($res_child_count))$this->res_child_count = $res_child_count;
		if(isset($res_child_last_date))$this->res_child_last_date = $res_child_last_date;
		if(isset($res_comment_count))$this->res_comment_count = $res_comment_count;
		if(isset($res_comment_last_date))$this->res_comment_last_date = $res_comment_last_date;
		if(isset($l_hash))$this->l_hash = $l_hash;
	}

	function save(): mixed {
		return (new ViewResArticleEntity)->save($this);
	}

	function insert(): mixed {
		return (new ViewResArticleEntity)->insert($this);
	}
}
