<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;

trait SearchLogTypeTrait {
	use PropertyInitTrait;

	var int $sl_id;
	var ?int $login_id;
	var string $sl_q;
	var string $sl_ip;
	var string $sl_entered;

	function __construct(?int $sl_id = null, ?int $login_id = null, ?string $sl_q = null, ?string $sl_ip = null, ?string $sl_entered = null) {
		if(isset($sl_id))$this->sl_id = $sl_id;
		if(isset($login_id))$this->login_id = $login_id;
		if(isset($sl_q))$this->sl_q = $sl_q;
		if(isset($sl_ip))$this->sl_ip = $sl_ip;
		if(isset($sl_entered))$this->sl_entered = $sl_entered;
	}

	function save(): mixed {
		return (new SearchLogEntity)->save($this);
	}

	function insert(): mixed {
		return (new SearchLogEntity)->insert($this);
	}

	function delete(): bool {
		return (new SearchLogEntity)->delete($this->sl_id);
	}

	function update(): bool {
		return (new SearchLogEntity)->update($this->sl_id, $this);
	}
}
