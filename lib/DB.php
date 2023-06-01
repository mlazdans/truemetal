<?php declare(strict_types = 1);

use dqdp\DBA\driver\MySQL_PDO;

enum DBFetchFunction: string
{
	case FetchAssoc = "FetchAssoc";
	case FetchObject = "FetchObject";
}

class DB
{
	private static MySQL_PDO $db;
	private static DBFetchFunction $fetch_function = DBFetchFunction::FetchAssoc;

	static function setDB(MySQL_PDO $db)
	{
		static::$db = $db;
	}

	static function getDB(): MySQL_PDO
	{
		return static::$db;
	}

	static function setFetchFunction(DBFetchFunction $function)
	{
		static::$fetch_function = $function;
	}

	static function Execute()
	{
		$q = static::Query(...func_get_args());
		while($r = static::{static::$fetch_function->value}($q)){
			$res[] = $r;
		}

		return $res ?? ($q->columnCount() ? null : (bool)$q);
	}

	static function ExecuteSingle()
	{
		$q = static::Query(...func_get_args());

		if($r = static::{static::$fetch_function->value}($q))
		{
			$res = $r;
		}

		return $res ?? ($q->columnCount() ? null : (bool)$q);
	}

	static function Quote($p)
	{
		return __object_map($p, function(mixed $item){
			return static::$db->escape((string)$item);
		});
	}

	static function LastID()
	{
		return static::$db->last_insert_id();
	}

	static function Now(): string
	{
		return 'CURRENT_TIMESTAMP';
	}

	static function withNewTrans(): mixed
	{
		return static::$db->with_new_trans(...func_get_args());
	}

	static function Query()
	{
		return static::$db->query(...func_get_args());
	}

	static function Prepare()
	{
		return static::$db->prepare(...func_get_args());
	}

	static function ExecutePrepared()
	{
		$q = static::$db->execute(...func_get_args());
		if($r = static::{static::$fetch_function->value}($q))
		{
			$res = $r;
		}

		return $res ?? ($q->columnCount() ? null : (bool)$q);
		// return static::$db->execute(...func_get_args());
	}

	static function Fetch($q)
	{
		return static::{static::$fetch_function->value}($q);
	}

	static function FetchAssoc($q)
	{
		return static::$db->fetch_assoc($q);
	}

	static function FetchObject($q)
	{
		return static::$db->fetch_object($q);
	}

	static function rowCount($q = null): int {
		if($q instanceof PDOStatement)
		{
			return $q->rowCount();
		}

		return static::$db->affected_rows();
	}
}
