<?php declare(strict_types = 1);

use dqdp\DBA\driver\MySQL_PDO;

enum DBFetchFunction: string
{
	case FetchAssoc = "fetch_assoc";
	case FetchObject = "fetch_object";
}

class DB
{
	private static MySQL_PDO $db;
	private static DBFetchFunction $fetch_function = DBFetchFunction::FetchAssoc;

	static function set_db(MySQL_PDO $db)
	{
		static::$db = $db;
	}

	static function get_db(): MySQL_PDO
	{
		return static::$db;
	}

	static function set_fetch_function(DBFetchFunction $function)
	{
		static::$fetch_function = $function;
	}

	static function execute()
	{
		$q = static::query(...func_get_args());
		while($r = static::{static::$fetch_function->value}($q)){
			$res[] = $r;
		}

		return $res ?? ($q->columnCount() ? null : (bool)$q);
	}

	static function execute_single()
	{
		$q = static::query(...func_get_args());

		if($r = static::{static::$fetch_function->value}($q))
		{
			$res = $r;
		}

		return $res ?? ($q->columnCount() ? null : (bool)$q);
	}

	static function quote($p)
	{
		return __object_map($p, function(mixed $item){
			return static::$db->escape((string)$item);
		});
	}

	static function last_id()
	{
		return static::$db->last_insert_id();
	}

	static function now(): string
	{
		return 'CURRENT_TIMESTAMP';
	}

	static function with_new_trans(): mixed
	{
		return static::$db->with_new_trans(...func_get_args());
	}

	static function query()
	{
		return static::$db->query(...func_get_args());
	}

	static function prepare()
	{
		return static::$db->prepare(...func_get_args());
	}

	static function execute_prepared()
	{
		$q = static::$db->execute(...func_get_args());
		if($r = static::{static::$fetch_function->value}($q))
		{
			$res = $r;
		}

		return $res ?? ($q->columnCount() ? null : (bool)$q);
		// return static::$db->execute(...func_get_args());
	}

	static function fetch($q)
	{
		return static::{static::$fetch_function->value}($q);
	}

	static function fetch_assoc($q)
	{
		return static::$db->fetch_assoc($q);
	}

	static function fetch_object($q)
	{
		return static::$db->fetch_object($q);
	}

	static function row_count($q = null): int {
		if($q instanceof PDOStatement)
		{
			return $q->rowCount();
		}

		return static::$db->affected_rows();
	}
}
