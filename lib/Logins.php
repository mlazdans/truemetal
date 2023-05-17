<?php declare(strict_types = 1);

use dqdp\SQL\Update;
use dqdp\TODO;

class Logins
{
	var $error_msg = [];

	static function load_single(LoginsFilter $F): ?LoginsType
	{
		return (new LoginsEntity)->getSingle($F);
	}

	static function load(LoginsFilter $F): LoginsCollection
	{
		return (new LoginsEntity)->getAll($F);
	}

	static function load_by_email(string $email, bool $ignore_disabled = false)
	{
		return static::load_single(LoginsFilter::ignore_disabled(new LoginsFilter(l_email: $email), $ignore_disabled));
	}

	static function load_by_id(int $id, bool $ignore_disabled = false)
	{
		return static::load_single(LoginsFilter::ignore_disabled(new LoginsFilter(l_id: $id), $ignore_disabled));
	}

	static function load_by_login(string $login, bool $ignore_disabled = false)
	{
		return static::load_single(LoginsFilter::ignore_disabled(new LoginsFilter(l_login: $login), $ignore_disabled));
	}

	static function load_by_nick(string $nick, bool $ignore_disabled = false)
	{
		return static::load_single(LoginsFilter::ignore_disabled(new LoginsFilter(l_nick: $nick), $ignore_disabled));
	}

	static function load_by_login_hash(string $hash, bool $ignore_disabled = false)
	{
		return static::load_single(LoginsFilter::ignore_disabled(new LoginsFilter(l_hash: $hash), $ignore_disabled));
	}

	static function load_by_sess_id(string $sess_id, bool $ignore_disabled = false)
	{
		return static::load_single(LoginsFilter::ignore_disabled(new LoginsFilter(l_sess_id: $sess_id), $ignore_disabled));
	}

	# TODO: vajadzētu MySQL collation, kas respektē garumzīmes?
	static function email_exists(string $email): bool {
		$data = static::load_by_email($email, true);

		return $data !== null;
	}

	static function login_exists(string $login): bool {
		$data = static::load_by_login($login, true);

		return $data !== null;
	}

	static function nick_exists(string $nick): bool {
		$data = static::load_by_nick($nick, true);

		return $data !== null;
	}

	static function banned24h($ip): bool
	{
		$item = DB::ExecuteSingle(
			"SELECT COUNT(*) banned FROM logins WHERE l_active = 0 AND l_userip = ? AND l_lastaccess > ?",
			$ip, date('Y-m-d H:i:s', strtotime('-10 minutes'))
		);

		return $item['banned'] > 0;
	}

	static function get_active()
	{
		$sql = sprintf("SELECT * FROM logins WHERE l_logedin = 'Y' AND '%s' < l_lastaccess",
			date('Y-m-d H:i:s', time() - 600)
		);

		return DB::Execute($sql);
	}

	static function delete_image(): bool
	{
		global $sys_user_root;

		$l_id = User::id();

		if(empty($l_id))
			return false;

		$ts = date('YmdHis');
		$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
		$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';

		$save_path1 = $sys_user_root.'/pic/'.$l_id.'-'.$ts.'.jpg';
		$tsave_path1 = $sys_user_root.'/pic/thumb/'.$l_id.'-'.$ts.'.jpg';

		if(file_exists($save_path))
			rename($save_path, $save_path1);
		if(file_exists($tsave_path))
			rename($tsave_path, $tsave_path1);

		return true;
	}

	function login(string $l_login, string $l_pass)
	{
		if($data = static::auth($l_login, $l_pass)) {
			return $data;
		}
	}

	static function logoff(): bool
	{
		if(User::logged())
		{
			session_destroy();
			return true;
		}

		return false;
	}

	static function genCode(): string {
		return strtoupper(md5(uniqid('')));
	}

	# TODO: apvienot accept un forget
	static function insert_accept_code(string $l_email, ?string $new_email = null): ?string
	{
		$LA = LoginAcceptType::initFrom(new LoginAcceptDummy(
			la_email: $l_email,
			la_new_email: $new_email,
			la_code: static::genCode(),
		));

		return $LA->insert() ? $LA->la_code : null;
	}

	static function insert_forgot_code(string $l_email): ?string
	{
		$F = LoginForgotType::initFrom(new LoginForgotDummy(
			f_email: $l_email,
			f_code: static::genCode(),
		));

		return $F->insert() ? $F->f_code : null;
	}

	static function send_forgot_code(string $l_email, string $code): bool
	{
		global $sys_domain;

		$t = new_template('emails/forgot.tpl');
		$t->set_var('ip', $_SERVER['REMOTE_ADDR']);
		$t->set_var('sys_domain', $sys_domain);
		$t->set_var('code', $code);
		$msg = $t->parse();

		$subj = "$sys_domain - aizmirsi paroli?";

		if(email($l_email, $subj, $msg))
		{
			return DB::Execute("UPDATE login_forgot SET f_sent = 1 WHERE f_email = ?", $l_email);
		}

		return false;
	}

	static function send_accept_code(string $l_email, string $to_email, string $subj, string $msg): bool
	{
		if(email($to_email, $subj, $msg))
		{
			return DB::Execute("UPDATE login_accept SET la_sent = 1 WHERE la_email = ?", $l_email);
		}

		return false;
	}

	static function accept_login(string $code): bool
	{
		$timeout = static::codes_timeout();

		$sql = "SELECT * FROM login_accept WHERE la_code = ? AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(la_entered) < ? AND la_accepted IS NULL";

		if($data = DB::ExecuteSingle($sql, $code, $timeout))
		{
			return DB::withNewTrans(function() use ($data) {
				$logins_update = (new Update('logins'))
					->Set('l_accepted', 1)
					->Where(['l_email = ?', $data['la_email']])
				;

				if($data['la_new_email'])
				{
					$logins_update->Set('l_email', $data['la_new_email']);
				}

				return
					DB::Execute("UPDATE login_accept SET la_accepted = CURRENT_TIMESTAMP WHERE la_email = ?", $data['la_email']) &&
					DB::Execute($logins_update);
			});
		}

		return false;
	}

	# TODO: configā
	static function codes_timeout(): int {
		return 900; // 15 min
	}

	static function get_forgot($code)
	{
		return DB::ExecuteSingle(
			"SELECT * FROM login_forgot WHERE f_code = ? AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(f_entered) < ?",
			 $code, static::codes_timeout()
		);
	}

	static function register(array $post_data): ?int
	{
		$L = LoginsType::initFrom(new LoginsDummy(
			l_hash: Logins::gen_login_hash(),
			l_login: $post_data['l_login'],
			l_nick: $post_data['l_nick'],
			l_password: Logins::gen_password_hash($post_data['l_password']),
			l_email: $post_data['l_email'],
			l_userip: User::ip()
		));

		$new_l_id = DB::withNewTrans(function() use ($L) {
			global $sys_domain;

			if(!($accept_code = Logins::insert_accept_code($L->l_email)))
			{
				return false;
			}

			$t = new_template('emails/registered.tpl');
			$t->set_var('ip', $L->l_userip);
			$t->set_var('sys_domain', $sys_domain);
			$t->set_var('code', $accept_code);
			$msg = $t->parse();

			$subj = "$sys_domain - reģistrācija";

			if(!Logins::send_accept_code($L->l_email, $L->l_email, $subj, $msg))
			{
				return false;
			}

			return $L->insert();
		});

		return $new_l_id ? $new_l_id : null;
	}

	function update(array $data, $validate = Res::ACT_VALIDATE): bool
	{
		new TODO("Logins::update");

		# TODO: pārbaudīt citur!!!
		if(!$this->valid_login($data['l_login']))
		{
			$this->error_msg[] = 'Nav norādīts vai nepareizs lietotāja logins';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE logins SET ';
		$sql .= "l_nick = '$data[l_nick]', ";
		$sql .= "l_email = '$data[l_email]', ";
		$sql .= "l_active = '$data[l_active]', ";
		$sql .= "l_accepted = '$data[l_accepted]', ";
		$sql .= "l_emailvisible = '$data[l_emailvisible]', ";
		$sql .= "l_logedin = '$data[l_logedin]', ";
		$sql = substr($sql, 0, -2);
		$sql .= " WHERE l_login = '$data[l_login]'";

		return DB::Execute($sql);
	}

	static function delete(int $l_id): bool
	{
		return DB::Execute("DELETE FROM logins WHERE l_id = ?", $l_id);
	}

	static function accept(int $l_id): bool
	{
		return DB::Execute("UPDATE logins SET l_accepted = 1 WHERE l_id = ?", $l_id);
	}

	static function activate(int $l_id): bool
	{
		return DB::Execute("UPDATE logins SET l_active = 1 WHERE l_id = ?", $l_id);
	}

	static function deactivate(int $l_id): bool
	{
		return DB::Execute("UPDATE logins SET l_active = 0 WHERE l_id = ?", $l_id);
	}

	function process_action(array $data, string $action): bool
	{
		if($action == 'delete_multiple')
			$func = 'del';

		if($action == 'activate_multiple')
			$func = 'activate';

		if($action == 'deactivate_multiple')
			$func = 'deactivate';

		if($action == 'accept_multiple')
			$func = 'accept';

		if(!isset($func) || empty($data['logins_count']))
		{
			return false;
		}

		return DB::withNewTrans(function() use ($func, $data) {
			for($r = 1; $r <= $data['logins_count']; ++$r)
			{
				if(isset($data['l_checked'.$r]) && isset($data['l_id'.$r]))
				{
					if(!$this->{$func}((int)$data['l_id'.$r]))
					{
						return false;
					}
				}
			}

			return true;
		});
	}

	static function valid_login($user_login)
	{
		return valid($user_login) && (strlen($user_login) > 0);
	}

	static function update_password(string $login, string $password): bool
	{
		return DB::Execute(
			"UPDATE logins SET l_password = ? WHERE l_login = ?",
			static::gen_password_hash($password),
			$login
		);
	}

	static function remove_forgot_code(string $code)
	{
		return DB::Execute("DELETE FROM login_forgot WHERE f_code = ?", $code);
	}

	static function collectUsersByIP($ips, $exclude_l_ids = array(), $exclude_ips = array(), $d = 0)
	{
		if($d > 3)
			return false;

		if(!is_array($ips))
			$ips = array($ips);

		if(!is_array($exclude_l_ids) && $exclude_l_ids)
			$exclude_l_ids = array($exclude_l_ids);

		if(!is_array($exclude_ips) && $exclude_ips)
			$exclude_ips = array($exclude_ips);

		$sql_add = '';
		if($exclude_l_ids)
			$sql_add .= " AND c.login_id NOT IN (".join(",", $exclude_l_ids).")";

		if($exclude_ips)
			$sql_add .= " AND c.c_userip NOT IN ('".join("','", $exclude_ips)."')";

		$ips_sql  = join("','", $ips);
		$exclude_sql = join("','", array_merge($ips, $exclude_ips));

		$sql = "
SELECT
	l.l_id,
	l.l_login,
	l.l_nick,
	l.l_active,
	COUNT(*) comment_count
FROM
	`comment` c
JOIN logins l ON l.l_id = c.login_id
WHERE
	c.c_userip IN ('$ips_sql')
	$sql_add
GROUP BY
	l.l_id
";

		if($alsoUsers = DB::Execute($sql))
		{
			return $alsoUsers;
		} else {
			return false;
		}
	}

	static function gen_password_hash(string $str): string {
		return password_hash($str, PASSWORD_BCRYPT, ['cost'=>12]);
	}

	static function auth(string $login, string $pass, bool $ignore_disabled = false): ?LoginsType
	{
		if($data = static::load_by_login($login, $ignore_disabled))
		{
			$pass_ok =
				password_verify($pass, $data->l_password) ||
				(mysql_password($pass) == $data->l_password) ||
				(mysql_old_password($pass) == $data->l_password)
			;

			if($pass_ok){
				return $data;
			}
		}

		return null;
	}

	static function gen_login_hash(): string
	{
		$hlen = 8;
		do {
			$l_hash = substr(md5uniqid(), 0, $hlen);
			$found = DB::ExecuteSingle("SELECT l_hash FROM logins WHERE l_hash = ?", $l_hash);
		} while($found);

		return $l_hash;
	}

}
