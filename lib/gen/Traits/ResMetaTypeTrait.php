<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait ResMetaTypeTrait {
	use PropertyInitTrait;

	var int $res_id;
	var ?int $res_votes;
	var ?int $res_votes_plus_count;
	var ?int $res_votes_minus_count;
	var ?int $res_child_count;
	var ?string $res_child_last_date;
	var ?int $res_comment_count;
	var ?string $res_comment_last_date;

	function __construct(?int $res_id = null, ?int $res_votes = null, ?int $res_votes_plus_count = null, ?int $res_votes_minus_count = null, ?int $res_child_count = null, ?string $res_child_last_date = null, ?int $res_comment_count = null, ?string $res_comment_last_date = null) {
		if(isset($res_id))$this->res_id = $res_id;
		if(isset($res_votes))$this->res_votes = $res_votes;
		if(isset($res_votes_plus_count))$this->res_votes_plus_count = $res_votes_plus_count;
		if(isset($res_votes_minus_count))$this->res_votes_minus_count = $res_votes_minus_count;
		if(isset($res_child_count))$this->res_child_count = $res_child_count;
		if(isset($res_child_last_date))$this->res_child_last_date = $res_child_last_date;
		if(isset($res_comment_count))$this->res_comment_count = $res_comment_count;
		if(isset($res_comment_last_date))$this->res_comment_last_date = $res_comment_last_date;
	}

	function save(): mixed {
		return (new ResMetaEntity)->save($this);
	}

	function insert(): mixed {
		return (new ResMetaEntity)->insert($this);
	}

	function delete(): bool {
		return (new ResMetaEntity)->delete($this->res_id);
	}

	function update(): bool {
		return (new ResMetaEntity)->update($this->res_id, $this);
	}
}
