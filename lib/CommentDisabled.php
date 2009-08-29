<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

class CommentDisabled
{
	static function get($login_id, $disable_login_id = 0)
	{
		global $db;

		$ret = array();

		$sql = sprintf("SELECT * FROM comment_disabled WHERE login_id = %d", $login_id);
		if($disable_login_id)
			$sql .= sprintf(" AND disable_login_id = %d", $disable_login_id);

		$data = $db->Execute($sql);
		foreach($data as $item)
			$ret[$item['disable_login_id']] = true;

		return $ret;
	} // isDisabled

	static function disable($login_id, $disable_login_id)
	{
		global $db;

		$sql = sprintf(
			"INSERT IGNORE INTO comment_disabled (login_id, disable_login_id) VALUES(%d, %d)",
			$login_id,
			$disable_login_id
			);

		return $db->Execute($sql);
	} // disable

	static function enable($login_id, $disable_login_id)
	{
		global $db;

		$sql = sprintf(
			"DELETE FROM comment_disabled WHERE login_id = %d AND disable_login_id = %d",
			$login_id,
			$disable_login_id
			);

		return $db->Execute($sql);
	} // enable

} // class::CommentDisabled

