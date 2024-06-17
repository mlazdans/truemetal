<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait ResRedirectTypeTrait
{
	use PropertyInitTrait;

	var int $from_res_id;
	var int $to_res_id;

	function __construct(?int $from_res_id = null, ?int $to_res_id = null)
	{
		if(isset($from_res_id))$this->from_res_id = $from_res_id;
		if(isset($to_res_id))$this->to_res_id = $to_res_id;
	}

	function save(): mixed {
		return (new ResRedirectEntity)->save($this);
	}

	function insert(): mixed {
		return (new ResRedirectEntity)->insert($this);
	}

	function delete(): bool {
		return (new ResRedirectEntity)->delete([$this->from_res_id, $this->to_res_id]);
	}

	function update(): bool {
		return (new ResRedirectEntity)->update([$this->from_res_id, $this->to_res_id], $this);
	}
}