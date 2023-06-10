<?php declare(strict_types = 1);

class SessHandler implements SessionHandlerInterface
{
	function open(string $path, string $name): bool
	{
		return true;
	}

	function close(): bool
	{
		return true;
	}

	function write(string $id, string $data): bool
	{
		global $ip;

		if(!User::logged())
		{
			return true;
		}

		if(empty($data))
		{
			$sql = "UPDATE logins SET l_sess_id = ?, l_sess_ip = ?, l_lastaccess = CURRENT_TIMESTAMP, l_logedin = 1 WHERE l_id = ?";
			return DB::Execute($sql, $id, $ip, User::id());
		} else {
			$sql = "UPDATE logins SET l_sess_id = ?, l_sessiondata = ?, l_sess_ip = ?, l_lastaccess = CURRENT_TIMESTAMP, l_logedin = 1 WHERE l_id = ?";
			return DB::Execute($sql, $id, $data, $ip, User::id());
		}
	}

	public function read(string $id): string|false
	{
		# TODO: ip check
		if($sess = Logins::load_by_sess_id($id))
		{
			User::data($sess);

			return $sess->l_sessiondata??"";
		}

		return "";
	}

	function destroy($sess_id): bool
	{
		return DB::Execute("UPDATE logins SET l_sess_id = NULL, l_logedin = 0 WHERE l_sess_id = ?", $sess_id);
	}

	function gc(int $max_lifetime): int|false
	{
		$period = date('Y-m-d', time() - $max_lifetime);

		DB::Execute("UPDATE logins SET l_sess_id = NULL, l_logedin = 0 WHERE l_lastaccess < ?", $period);

		return DB::rowCount();
	}
}
