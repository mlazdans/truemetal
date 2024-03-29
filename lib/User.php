<?php declare(strict_types = 1);

class User
{
	static private ?LoginsType $_LOGIN = null;
	static private ?int $entered_ts = null;

	static function data(?LoginsType $data = null): ?LoginsType
	{
		if(!is_null($data)){
			static::$_LOGIN = $data;
		}

		return static::$_LOGIN;
	}

	static function entered_ts(): int {
		return static::$entered_ts??(static::$entered_ts = strtotime(static::data()->l_entered));
	}

	static function logged(): bool
	{
		return !empty(static::get_val('l_id'));
	}

	static function id(): ?int
	{
		return static::get_val('l_id');
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

	static function get_val(string $k): mixed
	{
		return static::$_LOGIN->{$k} ?? null;
	}

	static function blacklisted(): bool
	{
		global $ip;

		# 1 week
		if(User::logged() && ((time() - strtotime(User::get_val('l_lastaccess'))) < 604800))
		{
			return false;
		} else {
			return ip_blacklisted($ip);
		}
	}

	static function ip(): string
	{
		global $ip;

		return $ip;
	}

	static function is_admin(): bool
	{
		return User::id() === 3;
	}

	static function can_edit_res(ViewResType $C){
		return User::is_admin() || (
			($C->login_id == User::id()) &&
			(time() - strtotime($C->res_entered) < 600)
		);
	}

	static function can_debug_res(ViewResType $R){
		return User::is_admin();
	}
}
