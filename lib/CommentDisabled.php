<?php declare(strict_types = 1);

use dqdp\SQL\Select;

class CommentDisabled
{
	public static function get(int $login_id, ?int $disable_login_id = null): array
	{
		$sql = (new Select)->From('comment_disabled')->Where(['login_id = ?', $login_id]);
		if($disable_login_id)
		{
			$sql->Where(['disable_login_id = ?', $disable_login_id]);
		}

		$q = DB::Query($sql);
		while($item = DB::Fetch($q)){
			$ret[$item['disable_login_id']] = true;
		}

		return $ret??[];
	}

	public static function disable(int $login_id, int $disable_login_id): bool
	{
		return DB::Execute(
			"INSERT IGNORE INTO comment_disabled (login_id, disable_login_id) VALUES(?, ?)",
			$login_id,
			$disable_login_id
		);
	}

	public static function enable(int $login_id, int $disable_login_id): bool
	{
		return DB::Execute(
			"DELETE FROM comment_disabled WHERE login_id = ? AND disable_login_id = ?",
			$login_id,
			$disable_login_id
		);
	}
}
