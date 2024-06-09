<?php declare(strict_types = 1);

class CommentDisabledEntity extends Entity
{
	use CommentDisabledEntityTrait;

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
