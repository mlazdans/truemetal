<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Res.php');
require_once('lib/Forum.php');

class Logins
{
	var $error_msg;

	const ACCEPTED = 'Y';
	const NOT_ACCEPTED = 'N';
	const EMAIL_VISIBLE = 'Y';
	const EMAIL_INVISIBLE = 'N';

	function __construct() {
	} // Logins

	function load(Array $params = array())
	{
		global $db;

		$sql_add = array();
		$sql_having = array();

		$params = $db->Quote($params);

		if(isset($params['l_id']))
			$sql_add[] = sprintf("l_id = %d", $params['l_id']);

		if(isset($params['l_login']))
			$sql_add[] = sprintf("l_login = '%s'", $params['l_login']);

		if(isset($params['l_email']))
			$sql_add[] = sprintf("l_email = '%s'", $params['l_email']);

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

		if(isset($params['l_password']))
			$sql_add[] = sprintf(
				"(l_password = PASSWORD('%s') OR l_password = OLD_PASSWORD('%s'))",
				$params['l_password'],
				$params['l_password']
				);

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
			$search_sql = search_to_sql($params['q'], array('l_nick', 'l_login', 'l_email', 'l_userip'));
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
			isset($params['single'])
			)
		{
			return $db->ExecuteSingle($sql);
		} else {
			return $db->Execute($sql);
		}
	} // load

	static function load_by_id_logged_in($l_id)
	{
		$Logins = new Logins();
		return $Logins->load(array(
			'l_id'=>$l_id,
			'l_logedin'=>'Y',
			));
	} // load_by_id_logged_in

	static function load_by_id($l_id)
	{
		$Logins = new Logins();
		return $Logins->load(array(
			'l_id'=>$l_id,
			));
	} // load_by_id

	static function load_by_login($l_login, $ADMIN = false)
	{
		$Logins = new Logins();
		if($ADMIN)
		{
			return $Logins->load(array(
				'l_login'=>$l_login,
				'l_active'=>Res::STATE_ALL,
				'l_accepted'=>Res::STATE_ALL,
				));
		} else {
			return $Logins->load(array(
				'l_login'=>$l_login,
				));
		}
	} // load_by_login

	static function load_by_email($l_email)
	{
		$Logins = new Logins();
		return $Logins->load(array(
			'l_email'=>$l_email,
			));
	} // load_by_email

	static function banned24h($ip)
	{
		global $db;

		$d = date('Y-m-d H:i:s', strtotime('-24 hours'));
		$sql = "SELECT COUNT(*) banned FROM `logins` WHERE l_active='N' AND l_userip='$ip' AND l_lastaccess > '$d'";
		$item = $db->ExecuteSingle($sql);

		return $item['banned'] > 0;
	} // banned24h

	function get_active()
	{
		global $db;

		$sql = sprintf(
			"SELECT * FROM logins WHERE l_logedin = 'Y' AND '%s' < l_lastaccess",
			date('Y-m-d H:i:s', time() - 600)
			);

		return $db->Execute($sql);
	} // get_active

	static function save_session_data($data = '')
	{
		global $db, $sess_handler;

		if(user_loged())
		{
			$l_id = $_SESSION['login']['l_id'];
			if(empty($data)){
				$data = session_encode();
			}
			$sql = "UPDATE logins SET l_sessiondata ='$data', l_lastaccess = NOW(), l_logedin = 'Y' WHERE l_id = $l_id";
			$db->Execute($sql);
			$db->Commit();
		}
	} // save_session_data

	static function delete_image()
	{
		global $sys_user_root;

		$l_id = user_loged() ? $_SESSION['login']['l_id'] : 0;

		if(!$l_id)
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

	function update_profile($data, $l_id = 0)
	{
		global $db, $sys_domain, $sys_user_root, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

		// check vai noraadiits id, vai ir ielogojies
		if(!$l_id)
		{
			$l_id_set = false;
			$l_id = user_loged() ? $_SESSION['login']['l_id'] : 0;
			if(!$l_id)
			{
				$this->error_msg = 'Neizdevās saglabāt profilu. Hacking?';
				return false;
			}
		} else {
			$l_id_set = true;
		}

		$error_msg = '';

		// load data
		if($l_data = Logins::load_by_id($l_id))
		{
			$this->validate($data);

			// check login status
			if($l_data['l_active'] != Res::STATE_ACTIVE || $l_data['l_accepted'] != Logins::ACCEPTED)
			{
				$error_msg .= 'Nevar saglabāt neaktīvu profilu!<br />';
			}

			// check pass match
			if($data['l_password'])
			{
				if($data['l_password'] != $data['l_password2'])
				{
					$error_msg .= 'Paroles nesakrīt!<br />';
				} elseif(invalid($data['l_password']) || strlen($data['l_password']) < 5) {
					$error_msg .= 'Nepareiza vai īsa parole!';
				}
			}

			// check email
			if(!valid_email($data['l_email']))
			{
				$error_msg .= 'Nekorekta e-pasta adrese!';
			}

		} else {
			$error_msg .= 'Nevar saglabāt neaktīvu kontu!<br />';
		}

		if(!$error_msg)
		{
			$osql = $sql = '';
			$sql .= $data['l_email'] ? "l_email = '$data[l_email]', " : '';
			$sql .= $data['l_password'] ? "l_password = PASSWORD('$data[l_password]'), " : '';
			$sql .= "l_emailvisible = '$data[l_emailvisible]', ";
			$sql .= $data['l_forumsort_themes'] ? "l_forumsort_themes = '$data[l_forumsort_themes]', " : '';
			$sql .= $data['l_forumsort_msg'] ? "l_forumsort_msg = '$data[l_forumsort_msg]', " : '';
			$sql .= "l_disable_avatars = $data[l_disable_avatars], ";
			$sql .= "l_disable_youtube = $data[l_disable_youtube], ";
			$osql .= $data['l_email'] ? "l_email = '$l_data[l_email]', " : '';
			$osql .= $data['l_password'] ? "l_password = '$l_data[l_password], " : '';

			# ja mainiits epasts, disable acc
			if($data['l_email'] && $data['l_email'] != $l_data['l_email'])
			{
				$sql .= "l_accepted = '".Logins::NOT_ACCEPTED."', ";
			}

			$sql = substr($sql, 0, -2);
			$osql = substr($osql, 0, -2);

			if($sql)
			{
				if($db->Execute("UPDATE logins SET $sql WHERE l_id = $l_id"))
				{
					# check new email changed
					if($data['l_email'] && ($data['l_email'] != $l_data['l_email']))
					{
						if($accept_code = $this->insert_accept_code($l_data['l_login']))
						{
							$msg = "Jūsu epasts tika mainīts!\n\nApstiprini jauno e-pasta adresi, atverot saiti http://$sys_domain/register/accept/$accept_code/";
							if(!$this->send_accept_code($l_data['l_login'], $accept_code, $data['l_email'], 'truemetal.lv e-pasta apstiprināšana', $msg))
							{
								$this->accept_login($accept_code);
								// rollback (god damn, mehehehheee)
								$db->Execute("UPDATE logins SET $osql WHERE l_id = $l_id");
								$this->error_msg = 'Nevar nosūtīt kodu uz "'.$data['l_email'].'"<br />('.$GLOBALS['php_errormsg'].')';
								return false;
							}
						}
					}

					# image
					if($_FILES['l_picfile']['tmp_name'])
					{
						Logins::delete_image();
						$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
						$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';
						# ja bilde
						if($ct = save_file('l_picfile', $save_path))
						{
							if(!($type = image_load($in_img, $save_path)))
							{
								$this->error_msg = 'Nevar nolasīt failu ['.$_FILES['l_picfile']['name'].']';
								if(isset($GLOBALS['image_load_error']) && $GLOBALS['image_load_error'])
									$this->error_msg .= " ($GLOBALS[image_load_error])";
								return false;
							}

							list($w, $h, $type, $html) = getimagesize($save_path);
							if($w > $user_pic_w || $h > $user_pic_h)
							{
								$out_img = image_resample($in_img, $user_pic_w, $user_pic_h);
								if(!image_save($out_img, $save_path, IMAGETYPE_JPEG))
								{
									$this->error_msg = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
									return false;
								}
							}

							if($w > $user_pic_tw || $h > $user_pic_th)
							{
								$out_img = image_resample($in_img, $user_pic_tw, $user_pic_th);
								if(!image_save($out_img, $tsave_path, IMAGETYPE_JPEG))
								{
									$this->error_msg = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
									return false;
								}
							}

							return Logins::load_by_id($l_id);
						} else {
							$this->error_msg = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
							return false;
						}
					}

					return Logins::load_by_id($l_id);
				}
			}
		} else {
			$this->error_msg = $error_msg;
			return false;
		}
	} // update_profile

	function login($l_login = '', $l_pass = '')
	{
		global $db;

		if(
			$this->valid_login($l_login) &&
			$l_pass &&
			($data = $this->load(array(
				'l_login'=>$l_login,
				'l_password'=>$l_pass,
				))
				)
			)
		{
			$db->Execute("UPDATE logins SET l_logedin ='Y' WHERE l_id = $data[l_id]");
			return $data;
		} else {
			$this->error_msg = 'Nepareizs login vai parole!';
			return array();
		}

	} // login

	static function logoff()
	{
		global $db;


		if(user_loged())
		{
			$l_id = $_SESSION['login']['l_id'];
			Logins::save_session_data();
			session_destroy();
			$db->Execute("UPDATE logins SET l_logedin ='N' WHERE l_id = $l_id");

			return true;
		}

		return false;
	} // logoff

	function insert_accept_code($login)
	{
		global $db;

		$accept_code = md5(uniqid(''));
		$sql = "INSERT INTO login_accept (la_login, la_code, la_entered) VALUES ('$login', '$accept_code', NOW());";
		if($db->Execute($sql))
		{
			return $accept_code;
		} else
			return false;
	} // insert_accept_code

	function insert_forgot_code($login)
	{
		global $db;

		$accept_code = md5(uniqid(''));
		$sql = "INSERT INTO login_forgot (f_login, f_code, f_entered) VALUES ('$login', '$accept_code', NOW());";
		if($db->Execute($sql))
		{
			return $accept_code;
		} else
			return false;
	} // insert_forgot_code

	function send_forgot_code($login, $code, $email, $subj = '', $msg = '')
	{
		global $sys_domain, $db;

		if(!$msg)
		{
			$msg = "Aizmirsi paroli?\n\nLogin: $login\nParole: uzpied uz http://$sys_domain/forgot/accept/$code/ un ievadi jaunu!\n\nIP:$_SERVER[REMOTE_ADDR]";
		}

		if(!$subj)
		{
			$subj = 'truemetal.lv - aizmirsi paroli?';
		}

		if(email($email, $subj, $msg))
		{
			$sql = "UPDATE login_forgot SET f_sent = 'Y' WHERE f_login = '$login'";
			return $db->Execute($sql);
		} else {
			return false;
		}
	} // send_forgot_code

	function send_accept_code($login, $code, $email, $subj = '', $msg = '')
	{
		global $sys_domain, $db;

		if(!$msg)
		{
			$msg = "Veiksmīga reģistrācija!\n\nApstiprini savu reģistrāciju, atverot šo saiti http://$sys_domain/register/accept/$code/";
		}

		if(!$subj)
		{
			$subj = 'truemetal.lv reģistrācija';
		}

		if(email($email, $subj, $msg))
		{
			$sql = "UPDATE login_accept SET la_sent = 'Y' WHERE la_login = '$login'";
			return $db->Execute($sql);
		} else {
			return false;
		}
	} // send_accept_code

	function accept_login($code, $timeout = 259200) // 3*24h
	{
		global $db;

		$sql = "SELECT * FROM login_accept WHERE la_code = '$code' AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(la_entered) < $timeout AND la_accepted = '0000-00-00 00:00:00'";

		if($data = $db->ExecuteSingle($sql))
		{
			$sql = "UPDATE login_accept SET la_accepted = NOW() WHERE la_login = '$data[la_login]'";
			$db->Execute($sql);
			$sql = "UPDATE logins SET l_accepted = '".Logins::ACCEPTED."' WHERE l_login = '$data[la_login]'";
			if($db->Execute($sql))
				return true;
		}

		return false;
	} // accept_login

	function get_forgot($code, $timeout = 259200) // 3*24h
	{
		global $db;

		$sql = "SELECT * FROM login_forgot WHERE f_code = '$code' AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(f_entered) < $timeout";

		return $db->ExecuteSingle($sql);
	} // get_forgot

	function insert(&$data, $validate = Res::ACT_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$data = $db->Quote($data);

		$date = $db->now();
		if($data['l_entered'])
			$date = "'$data[l_entered]'";

		$sql = "
		INSERT INTO logins (
			l_login, l_password, l_email,
			l_active, l_accepted, l_nick,
			l_entered, l_userip
		) VALUES (
			'$data[l_login]', PASSWORD('$data[l_password]'), '$data[l_email]',
			'$data[l_active]', '$data[l_accepted]', '$data[l_nick]',
			$date, '$_SERVER[REMOTE_ADDR]'
		)";

		if($db->Execute($sql))
		{
			if($accept_code = $this->insert_accept_code($data['l_login']))
				$this->send_accept_code($data['l_login'], $accept_code, $data['l_email']);

			return $data['l_login'];
		} else
			return false;
	} // insert

	function update($data, $validate = Res::ACT_VALIDATE)
	{
		global $db;

		if(!$this->valid_login($data['l_login']))
		{
			$this->error_msg = 'Nav norādīts vai nepareizs lietotāja logins<br />';
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

		$db->Execute($sql);

		return $data['l_login'];
	} // update

	function del($l_id)
	{
		global $db;

		if(!$l_id)
			return true;

		$sql = "DELETE FROM logins WHERE l_id = '$l_id'";

		return $db->Execute($sql);
	} // del

	function accept($l_id)
	{
		global $db;

		$sql = 'UPDATE logins SET l_accepted = "'.Logins::ACCEPTED.'" WHERE l_id = "'.$l_id.'"';

		return $db->Execute($sql);
	} // accept

	function activate($l_id)
	{
		global $db;

		$sql = 'UPDATE logins SET l_active = "'.Res::STATE_ACTIVE.'" WHERE l_id = "'.$l_id.'"';

		return $db->Execute($sql);
	} // activate

	function deactivate($l_id)
	{
		global $db;

		$sql = 'UPDATE logins SET l_active = "'.Res::STATE_INACTIVE.'" WHERE l_id = "'.$l_id.'"';

		return $db->Execute($sql);
	} // deactivate

	function process_action(&$data, $action)
	{
		$ret = true;
		$func = '';

		if($action == 'delete_multiple')
			$func = 'del';

		if($action == 'activate_multiple')
			$func = 'activate';

		if($action == 'deactivate_multiple')
			$func = 'deactivate';

		if($action == 'accept_multiple')
			$func = 'accept';

		if(isset($data['logins_count']) && $func)
			for($r = 1; $r <= $data['logins_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['l_checked'.$r]) && isset($data['l_id'.$r]))
					$ret = $ret && $this->{$func}($data['l_id'.$r]);

		return $ret;
	} // process_action

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

		if(isset($data['l_disable_avatars']))
			$data['l_disable_avatars'] = 1;
		else
			$data['l_disable_avatars'] = 0;

		if(isset($data['l_disable_youtube']))
			$data['l_disable_youtube'] = 1;
		else
			$data['l_disable_youtube'] = 0;

	} // validate

	static function valid_login($user_login)
	{
		return valid($user_login) && (strlen($user_login) > 0);
	} // valid_login

	function update_password($login, $password)
	{
		global $db;

		$sql = "UPDATE logins SET l_password = PASSWORD('$password') WHERE l_login='$login'";

		return $db->Execute($sql);
	} // update_password

	function remove_forgot_code($code)
	{
		global $db;

		$sql = "DELETE FROM login_forgot WHERE f_code='$code'";

		return $db->Execute($sql);
	} // remove_forgot_code

	static function collectUsersByIP($ips, $exclude_l_ids = array(), $exclude_ips = array(), $d = 0)
	{
		global $db;

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

		if($alsoUsers = $db->Execute($sql))
		{
			return $alsoUsers;
		} else {
			return false;
		}
	} // collectUsersByIP
} // class Logins

