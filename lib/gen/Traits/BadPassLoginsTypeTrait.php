<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait BadPassLoginsTypeTrait {
	use PropertyInitTrait;

	var string $pass_hash;
	var int $l_id;

	function __construct(?string $pass_hash = null, ?int $l_id = null) {
		if(isset($pass_hash))$this->pass_hash = $pass_hash;
		if(isset($l_id))$this->l_id = $l_id;
	}

	function save(): mixed {
		return (new BadPassLoginsEntity)->save($this);
	}

	function insert(): mixed {
		return (new BadPassLoginsEntity)->insert($this);
	}

	function delete(): bool {
		return (new BadPassLoginsEntity)->delete([$this->pass_hash, $this->l_id]);
	}

	function update(): bool {
		return (new BadPassLoginsEntity)->update([$this->pass_hash, $this->l_id], $this);
	}
}