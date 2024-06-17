<?php declare(strict_types = 1);

abstract class AbstractResEntity extends Entity
{
	function __construct(){
		parent::__construct();
	}

	abstract static function get_by_id(int $id, bool $ignore_disabled = false);
	abstract static function get_by_res_id(int $res_id, bool $ignore_disabled = false);
	abstract static function get_by_hash(string $hash, bool $ignore_disabled = false);
}
