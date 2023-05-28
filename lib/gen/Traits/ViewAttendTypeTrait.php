<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;
use dqdp\DBA\Types\Varchar;

trait ViewAttendTypeTrait {
	use PropertyInitTrait;

	readonly int $l_id;
	readonly int $res_id;
	readonly int $a_attended;
	readonly string $a_entered;
	readonly string $l_nick;
	readonly ?string $l_hash;

	function __construct(array|object|null $data = null, array|object|null $defaults = null) {
		parent::__construct($data, $defaults);
		if(!prop_initialized($this, 'a_attended'))$this->a_attended = (int)1;
	}

	static function initl_nick(mixed $v): string {
		return (string)(new VarChar($v, 16));
	}

	static function initl_hash(mixed $v): string {
		return (string)(new VarChar($v, 8));
	}

	function save(): mixed {
		return (new ViewAttendEntity)->save($this);
	}

	function insert(): mixed {
		return (new ViewAttendEntity)->insert($this);
	}
}
