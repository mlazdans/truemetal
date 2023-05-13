<?php declare(strict_types = 1);

use dqdp\SQL\Select;
use dqdp\SQL\Update;

class Logins
{
	var $error_msg = [];

	const ACCEPTED = 'Y';
	const NOT_ACCEPTED = 'N';
	const EMAIL_VISIBLE = 'Y';
	const EMAIL_INVISIBLE = 'N';

	# TODO: bind params
	function load(Array $params = array())
	{
		$sql_add = array();
		$sql_having = array();

		$params = DB::Quote($params);

		if(isset($params['l_id']))
			$sql_add[] = sprintf("l_id = %d", $params['l_id']);

		if(isset($params['l_login']))
			$sql_add[] = sprintf("l_login = '%s'", $params['l_login']);

		if(isset($params['l_email']))
			$sql_add[] = sprintf("l_email = '%s'", $params['l_email']);

		if(isset($params['l_hash']))
			$sql_add[] = sprintf("l_hash = '%s'", $params['l_hash']);

		if(isset($params['l_nick']))
			$sql_add[] = sprintf("l_nick = '%s'", $params['l_nick']);

		if(isset($params['l_logedin']))
			$sql_add[] = sprintf("l_logedin = '%s'", $params['l_logedin']);

		if(isset($params['l_lastaccess']))
			$sql_add[] = sprintf("l_lastaccess = '%s'", $params['l_lastaccess']);

		if(!empty($params['jubilars']))
		{
			$d0 = date('Y-m-d H:i:s', strtotime("-6 month"));
			$d1 = date('Y-m-d H:i:s', strtotime("-2 day"));
			$d2 = date('Y-m-d H:i:s', strtotime("+2 day"));
			$sql_add[] = "(DATE_FORMAT(l_entered, '%m%d') >= DATE_FORMAT('$d1', '%m%d') AND DATE_FORMAT(l_entered, '%m%d') <= DATE_FORMAT('$d2', '%m%d'))";
			$sql_add[] = sprintf("l_lastaccess >= '%s'", $d0);
		}

		if(isset($params['l_active']))
		{
			if($params['l_active'] != Res::STATE_ALL)
				$sql_add[] = sprintf("l_active = '%s'", $params['l_active']);
		} else {
			$sql_add[] = sprintf("l_active = '%s'", Res::STATE_ACTIVE);
		}

		if(isset($params['l_accepted']))
		{
			if($params['l_accepted'] != Res::STATE_ALL)
				$sql_add[] = sprintf("l_accepted = '%s'", $params['l_accepted']);
		} else {
			$sql_add[] = sprintf("l_accepted = '%s'", Logins::ACCEPTED);
		}

		if(isset($params['q']))
		{
			$search_sql = search_to_sql_legacy($params['q'], array('l_nick', 'l_login', 'l_email', 'l_userip'));
			if($search_sql)
				$sql_add[] = $search_sql;
		}

		$sql = " SELECT * ";

		if(!empty($params['jubilars'])){
			$sql .= ", DATE_FORMAT(l_entered, '%m%d') AS entered_stamp ";
			$sql .= ", DATEDIFF(CURRENT_TIMESTAMP, l_entered) AS age ";
		}

		if(!empty($params['get_all_ips']))
		{
			$d = date('Y-m-d H:i:s', strtotime('-1 year'));
			$sql .= ", (SELECT GROUP_CONCAT(DISTINCT c_userip) FROM comment WHERE login_id = l_id AND c_entered > '$d') all_ips ";
		}

		$sql .= " FROM logins ";

		if(!empty($params['get_comment_count']))
		{
			if(isset($params['comment_count_more_than']))
			{
				$sql_add[] = sprintf("comment_count > %d", $params['comment_count_more_than']);
			}
			if(isset($params['comment_count_equal']))
			{
				$sql_add[] = sprintf("comment_count = %d", $params['comment_count_equal']);
			}
			if(isset($params['comment_count_less_than']))
			{
				$sql_add[] = sprintf("comment_count < %d", $params['comment_count_less_than']);
			}
		}

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		if($sql_having)
			$sql .= " HAVING ".join(" AND ", $sql_having);

		if(empty($params['order']))
		{
			if(!empty($params['jubilars'])){
				$sql .= " ORDER BY entered_stamp ASC ";
			} else {
				$sql .= " ORDER BY l_entered DESC ";
			}
		} else {
			$sql .= " ORDER BY $params[order] ";
		}

		if(isset($params['limit'])){
			$sql .= " LIMIT $params[limit]";
		}

		if(
			isset($params['l_id']) ||
			isset($params['l_login']) ||
			isset($params['l_email']) ||
			isset($params['l_hash']) ||
			isset($params['single'])
			)
		{
			return DB::ExecuteSingle($sql);
		} else {
			return DB::Execute($sql);
		}
	}

	static function load_by_id(int $l_id, bool $all = false)
	{
		$params = [ 'l_id'=>$l_id ];

		if($all)
		{
			$params['l_active'] = Res::STATE_ALL;
			$params['l_accepted'] = Res::STATE_ALL;
		}

		return (new Logins)->load($params);
	}

	static function load_by_login(string $login, bool $all = false)
	{
		$params = [ 'l_login'=>$login ];

		if($all)
		{
			$params['l_active'] = Res::STATE_ALL;
			$params['l_accepted'] = Res::STATE_ALL;
		}

		return (new Logins)->load($params);
	}

	static function load_by_nick(string $nick, bool $all = false)
	{
		$params = [ 'l_nick'=>$nick ];

		if($all)
		{
			$params['l_active'] = Res::STATE_ALL;
			$params['l_accepted'] = Res::STATE_ALL;
		}

		return (new Logins)->load($params);
	}

	static function load_by_login_hash(string $l_hash)
	{
		return (new Logins)->load(['l_hash'=>$l_hash]);
	}

	# TODO: vajadzētu MySQL collation, kas respektē garumzīmes?
	static function email_exists(string $email): bool {
		$data = static::load_by_email($email, true);

		return $data && (count($data) > 0);
	}

	static function login_exists(string $login): bool {
		$data = static::load_by_login($login, true);

		return $data && (count($data) > 0);
	}

	static function nick_exists(string $nick): bool {
		$data = static::load_by_nick($nick, true);

		return $data && (count($data) > 0);
	}

	static function load_by_email(string $email, bool $all = false)
	{
		$params = [ 'l_email'=>$email ];

		if($all)
		{
			$params['l_active'] = Res::STATE_ALL;
			$params['l_accepted'] = Res::STATE_ALL;
		}

		return (new Logins)->load($params);
	}

	static function banned24h($ip): bool
	{
		$item = DB::ExecuteSingle(
			"SELECT COUNT(*) banned FROM logins WHERE l_active = ? AND l_userip = ? AND l_lastaccess > ?",
			Res::STATE_INACTIVE, $ip, date('Y-m-d H:i:s', strtotime('-10 minutes'))
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

	static function delete_image()
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
	} // delete_image

	function update_profile($data, ?int $l_id = null): bool
	{
		global $sys_user_root, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

		if(empty($l_id))
		{
			$l_id = User::id();
		}

		if(empty($l_id))
		{
			$this->error_msg[] = 'Neizdevās saglabāt profilu. Hacking?';
			return false;
		}

		// load data
		$OLD = Logins::load_by_id($l_id);

		if(empty($OLD))
		{
			$this->error_msg[] = 'Konts nav atrasts vai ir neaktīvs!';
			return false;
		}

		$this->validate($data);

		if($this->error_msg)
		{
			return false;
		}

		$UPDATE = (new Update('logins'))
			->Set("l_emailvisible", $data['l_emailvisible'])
			->Set("l_disable_youtube", $data['l_disable_youtube'])
			->Where(["l_id = ?", $l_id])
		;

		if($data['l_forumsort_themes'])$UPDATE->Set("l_forumsort_themes", $data['l_forumsort_themes']);
		if($data['l_forumsort_msg'])$UPDATE->Set("l_forumsort_msg", $data['l_forumsort_msg']);

		if(!DB::Execute($UPDATE))
		{
			$this->error_msg[] = "Datubāzes kļūda";
			return false;
		}

		# TODO: db FS
		if($_FILES['l_picfile']['tmp_name'])
		{
			Logins::delete_image();
			$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
			$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';
			# ja bilde
			if($ct = save_upload('l_picfile', $save_path))
			{
				if(!($type = image_load($in_img, $save_path)))
				{
					$this->error_msg[] = 'Nevar nolasīt failu ['.$_FILES['l_picfile']['name'].']';
					if(isset($GLOBALS['image_load_error']) && $GLOBALS['image_load_error'])
						$this->error_msg[] = " ($GLOBALS[image_load_error])";
					return false;
				}

				list($w, $h, $type, $html) = getimagesize($save_path);
				if($w > $user_pic_w || $h > $user_pic_h)
				{
					$out_img = image_resample($in_img, $user_pic_w, $user_pic_h);
					if(!image_save($out_img, $save_path, IMAGETYPE_JPEG))
					{
						$this->error_msg[] = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
						return false;
					}
				}

				if($w > $user_pic_tw || $h > $user_pic_th)
				{
					$out_img = image_resample($in_img, $user_pic_tw, $user_pic_th);
					if(!image_save($out_img, $tsave_path, IMAGETYPE_JPEG))
					{
						$this->error_msg[] = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
						return false;
					}
				}

				return true;
			} else {
				$this->error_msg[] = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
				return false;
			}
		}

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

	static function insert_accept_code($login, $new_email = null): ?string
	{
		$accept_code = static::genCode();

		return DB::Execute(
			"INSERT INTO login_accept (la_login, la_new_email, la_code, la_entered) VALUES (?, ?, ?, CURRENT_TIMESTAMP)",
				$login, $new_email, $accept_code
		) ? $accept_code : null;
	}

	static function insert_forgot_code($login): ?string
	{
		$accept_code = static::genCode();

		return DB::Execute(
			"INSERT INTO login_forgot (f_login, f_code, f_entered) VALUES (?, ?, CURRENT_TIMESTAMP)",
			$login, $accept_code
		) ? $accept_code : null;
	}

	static function send_forgot_code($login, $code, $email): bool
	{
		global $sys_domain;

		$t = new_template('emails/forgot.tpl');
		$t->set_var('ip', $_SERVER['REMOTE_ADDR']);
		$t->set_var('login', $login);
		$t->set_var('sys_domain', $sys_domain);
		$t->set_var('code', $code);
		$msg = $t->parse();

		$subj = "$sys_domain - aizmirsi paroli?";

		if(email($email, $subj, $msg))
		{
			return DB::Execute("UPDATE login_forgot SET f_sent = 'Y' WHERE f_login = ?", $login);
		}

		return false;
	}

	static function send_accept_code($login, $email, $subj, $msg): bool
	{
		if(email($email, $subj, $msg))
		{
			return DB::Execute("UPDATE login_accept SET la_sent = 'Y' WHERE la_login = ?", $login);
		}

		return false;
	}

	static function accept_login($code): bool
	{
		$timeout = static::codes_timeout();

		$sql = "SELECT * FROM login_accept WHERE la_code = '$code' AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(la_entered) < $timeout AND la_accepted = '0000-00-00 00:00:00'";

		if($data = DB::ExecuteSingle($sql))
		{
			return DB::withNewTrans(function() use ($data) {
				$logins_update = (new Update('logins'))
					->Set('l_accepted', Logins::ACCEPTED)
					->Where(['l_login = ?', $data['la_login']])
				;

				if($data['la_new_email'])
				{
					$logins_update->Set('l_email', $data['la_new_email']);
				}

				return
					DB::Execute("UPDATE login_accept SET la_accepted = CURRENT_TIMESTAMP WHERE la_login = ?", $data['la_login']) &&
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

	function insert($data, $validate = Res::ACT_VALIDATE): bool
	{
		if($validate){
			$this->validate($data);
		}

		$data['l_hash'] = Logins::gen_login_hash();

		return DB::withNewTrans(function() use ($data) {
			global $sys_domain, $ip;

			if(!($accept_code = $this->insert_accept_code($data['l_login'])))
			{
				return false;
			}

			$t = new_template('emails/registered.tpl');
			$t->set_var('ip', $_SERVER['REMOTE_ADDR']);
			$t->set_var('sys_domain', $sys_domain);
			$t->set_var('code', $accept_code);
			$msg = $t->parse();

			$subj = "$sys_domain - reģistrācija";

			if(!$this->send_accept_code($data['l_login'], $data['l_email'], $subj, $msg))
			{
				return false;
			}

			$sql = "INSERT INTO logins (
				l_login, l_hash, l_email,
				l_active, l_accepted, l_nick,
				l_userip, l_entered
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP
			)";

			$args = [
				$data['l_login'], $data['l_hash'], $data['l_email'], $data['l_active'],
				$data['l_accepted'], $data['l_nick'], $ip
			];

			if(DB::Execute($sql, ...$args))
			{
				if($this->update_password($data['l_login'], $data['l_password']))
				{
					return true;
				}
			}

			return false;
		});
	}

	function update(array $data, $validate = Res::ACT_VALIDATE): bool
	{
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
		return DB::Execute("UPDATE logins SET l_accepted = ? WHERE l_id = ?", Logins::ACCEPTED, $l_id);
	}

	static function activate(int $l_id): bool
	{
		return DB::Execute("UPDATE logins SET l_active = ? WHERE l_id = ?", Res::STATE_ACTIVE, $l_id);
	}

	static function deactivate(int $l_id): bool
	{
		return DB::Execute("UPDATE logins SET l_active = ? WHERE l_id = ?", Res::STATE_INACTIVE, $l_id);
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

	function validate(&$data)
	{
		if(isset($data['l_active']))
			$data['l_active'] = (preg_match('/[YN]/', $data['l_active']) ? $data['l_active'] : '');
		else
			$data['l_active'] = Res::STATE_ACTIVE;

		if(isset($data['l_emailvisible']))
			$data['l_emailvisible'] = Logins::EMAIL_VISIBLE;
		else
			$data['l_emailvisible'] = Logins::EMAIL_INVISIBLE;

		if(!isset($data['l_login']))
			$data['l_login'] = '';

		if(!isset($data['l_password']))
			$data['l_password'] = '';

		if(!isset($data['l_firstname']))
			$data['l_firstname'] = '';

		if(!isset($data['l_lastname']))
			$data['l_lastname'] = '';

		if(!isset($data['l_phone']))
			$data['l_phone'] = '';

		if(!isset($data['l_email']))
			$data['l_email'] = '';

		if(!isset($data['l_birth']))
			$data['l_birth'] = '';

		if(isset($data['l_type']))
			$data['l_type'] = (preg_match('/[\d]*/', $data['l_type']) ?  (int)$data['l_type'] : 0);
		else
			$data['l_type'] = 0;

		if(isset($data['l_spec']))
			$data['l_spec'] = (preg_match('/[\d]*/', $data['l_spec']) ? (int)$data['l_spec'] : 0);
		else
			$data['l_spec'] = 0;

		if(!isset($data['l_sertnr']))
			$data['l_sertnr'] = '';

		if(!isset($data['l_sertexpire']))
			$data['l_sertexpire'] = '';

		if(!isset($data['l_entered']))
			$data['l_entered'] = '';

		if(isset($data['l_accepted']))
			$data['l_accepted'] = (preg_match('/[YN]/', $data['l_accepted']) ? $data['l_accepted'] : '');
		else
			$data['l_accepted'] = Logins::NOT_ACCEPTED;

		if(isset($data['l_forumsort_themes']))
			$data['l_forumsort_themes'] = (preg_match('/[TC]/', $data['l_forumsort_themes']) ? $data['l_forumsort_themes'] : '');
		else
			$data['l_forumsort_themes'] = Forum::SORT_THEME;

		if(isset($data['l_forumsort_msg']))
			$data['l_forumsort_msg'] = (preg_match('/[AD]/', $data['l_forumsort_msg']) ? $data['l_forumsort_msg'] : '');
		else
			$data['l_forumsort_msg'] = Forum::SORT_THEME;

		if(isset($data['l_disable_youtube']))
			$data['l_disable_youtube'] = 1;
		else
			$data['l_disable_youtube'] = 0;

	} // validate

	static function valid_login($user_login)
	{
		return valid($user_login) && (strlen($user_login) > 0);
	}

	static function update_password(string $login, string $password)
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

	static function auth(string $login, string $pass, bool $all = false) {
		$sql = (new Select)->From("logins")->Where(["l_login = ?", $login]);

		if(!$all){
			$sql->Where(["l_active = ?", Res::STATE_ACTIVE]);
			$sql->Where(["l_accepted = ?", Logins::ACCEPTED]);
		}

		if($data = DB::ExecuteSingle($sql)){
			$pass_ok =
				password_verify($pass, $data['l_password']) ||
				(mysql_password($pass) == $data['l_password']) ||
				(mysql_old_password($pass) == $data['l_password'])
			;

			if($pass_ok){
				return $data;
			}
		}
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
