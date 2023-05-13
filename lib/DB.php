<?php declare(strict_types = 1);

use dqdp\DBA\driver\MySQL_PDO;

class DB
{
	private static MySQL_PDO $db;

	static function set_db(MySQL_PDO $db)
	{
		static::$db = $db;
	}

	static function Execute()
	{
		$q = static::$db->query(...func_get_args());
		while($r = static::$db->fetch_assoc($q)){
			$res[] = $r;
		}

		return $res ?? ($q->columnCount() ? [] : (bool)$q);
	}

	static function ExecuteSingle()
	{
		$q = static::$db->query(...func_get_args());
		if($r = static::$db->fetch_assoc($q))
		{
			$res = $r;
		}

		return $res ?? ($q->columnCount() ? [] : (bool)$q);
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

	static function Query($sql)
	{
		return static::$db->query($sql);
	}

	static function Prepare()
	{
		return static::$db->prepare(...func_get_args());
	}

	static function ExecutePrepared()
	{
		return static::$db->execute(...func_get_args());
	}

	static function Fetch($q)
	{
		return static::FetchAssoc($q);
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
