<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait ViewMainpageTypeTrait {
	use PropertyInitTrait;

	var string $module_id;
	var ?int $res_id;
	var int $doc_id;
	var ?string $res_name;
	var ?string $res_intro;
	var ?string $res_data;
	var ?string $res_entered;
	var int $res_kind;
	var ?int $res_comment_count;
	var ?string $res_comment_last_date;
	var ?string $type_id;
	var ?string $res_route;

	function __construct(?string $module_id = null, ?int $res_id = null, ?int $doc_id = null, ?string $res_name = null, ?string $res_intro = null, ?string $res_data = null, ?string $res_entered = null, ?int $res_kind = null, ?int $res_comment_count = null, ?string $res_comment_last_date = null, ?string $type_id = null, ?string $res_route = null) {
		if(isset($module_id))$this->module_id = $module_id;
		if(isset($res_id))$this->res_id = $res_id;
		if(isset($doc_id))$this->doc_id = $doc_id;
		if(isset($res_name))$this->res_name = $res_name;
		if(isset($res_intro))$this->res_intro = $res_intro;
		if(isset($res_data))$this->res_data = $res_data;
		if(isset($res_entered))$this->res_entered = $res_entered;
		if(isset($res_kind))$this->res_kind = $res_kind;
		if(isset($res_comment_count))$this->res_comment_count = $res_comment_count;
		if(isset($res_comment_last_date))$this->res_comment_last_date = $res_comment_last_date;
		if(isset($type_id))$this->type_id = $type_id;
		if(isset($res_route))$this->res_route = $res_route;
	}

	function save(): mixed {
		return (new ViewMainpageEntity)->save($this);
	}

	function insert(): mixed {
		return (new ViewMainpageEntity)->insert($this);
	}
}
