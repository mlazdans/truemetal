<?php declare(strict_types = 1);

class User
{
	static private array $_LOGIN = [];

	static function data(?array $data = null): array
	{
		if(!is_null($data)){
			static::$_LOGIN = $data;
		}

		return static::$_LOGIN;
	}

	static function logged(): bool
	{
		return !empty(static::$_LOGIN['l_id']);
	}

	static function id(): ?int
	{
		return ($v = static::get_val('l_id')) ? (int)$v : $v;
	}

	static function nick(): ?string
	{
		return static::get_val('l_nick');
	}

	static function login(): ?string
	{
		return static::get_val('l_login');
	}

	static function email(): ?string
	{
		return static::get_val('l_email');
	}

	static function isset(string $k): bool
	{
		return isset(static::$_LOGIN['l_id'][$k]);
	}

	static function get_val(string $k): mixed
	{
		return static::$_LOGIN[$k] ?? null;
	}
}
