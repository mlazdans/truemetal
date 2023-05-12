<?php declare(strict_types = 1);

class CommentDisabled
{
	public static function get($login_id, $disable_login_id = 0)
	{
		$ret = array();

		$sql = sprintf("SELECT * FROM comment_disabled WHERE login_id = %d", $login_id);
		if($disable_login_id)
			$sql .= sprintf(" AND disable_login_id = %d", $disable_login_id);

		$data = DB::Execute($sql);
		foreach($data as $item)
			$ret[$item['disable_login_id']] = true;

		return $ret;
	}

	public static function disable($login_id, $disable_login_id)
	{
		$sql = sprintf(
			"INSERT IGNORE INTO comment_disabled (login_id, disable_login_id) VALUES(%d, %d)",
			$login_id,
			$disable_login_id
			);

		return DB::Execute($sql);
	}

	public static function enable($login_id, $disable_login_id)
	{
		$sql = sprintf(
			"DELETE FROM comment_disabled WHERE login_id = %d AND disable_login_id = %d",
			$login_id,
			$disable_login_id
			);

		return DB::Execute($sql);
	}
}
