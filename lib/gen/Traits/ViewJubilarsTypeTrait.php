<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;
use dqdp\DBA\Types\Varchar;

trait ViewJubilarsTypeTrait {
	use PropertyInitTrait;

	readonly ?string $entered_stamp;
	readonly ?int $age;
	readonly int $l_id;
	readonly string $l_nick;
	readonly ?string $l_hash;

	function __construct(array|object|null $data = null, array|object|null $defaults = null) {
		parent::__construct($data, $defaults);
	}

	static function initentered_stamp(mixed $v): string {
		return (string)(new VarChar($v, 4));
	}

	static function initl_nick(mixed $v): string {
		return (string)(new VarChar($v, 16));
	}

	static function initl_hash(mixed $v): string {
		return (string)(new VarChar($v, 8));
	}

	function save(): mixed {
		return (new ViewJubilarsEntity)->save($this);
	}

	function insert(): mixed {
		return (new ViewJubilarsEntity)->insert($this);
	}
}
