<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

define('LOGIN_ACTIVE', 'Y');
define('LOGIN_INACTIVE', 'N');
define('LOGIN_ACCEPTED', 'Y');
define('LOGIN_NOTACCEPTED', 'N');
define('LOGIN_ALL', false);
define('LOGIN_VALIDATE', true);
define('LOGIN_DONTVALIDATE', false);
define('LOGIN_EMAIL_VISIBLE', 'Y');
define('LOGIN_EMAIL_INVISIBLE', 'N');

require_once('../classes/class.Forum.php');

class Logins
{
	var $error_msg;

	function Logins()
	{
	} // Logins

	function load($l_login = '', $l_pass = '', $l_active = LOGIN_ACTIVE, $l_accepted = LOGIN_ACCEPTED)
	{
		global $db;

		$sql_add = '';
		$sql = 'SELECT * FROM logins';

		if($this->valid_login($l_login))
			$sql_add .= "l_login = '$l_login' AND ";

		if($l_pass)
			$sql_add .= "(l_password = PASSWORD('$l_pass') OR l_password = OLD_PASSWORD('$l_pass')) AND ";

		if($l_active)
			$sql_add .= "l_active = '$l_active' AND ";

		if($l_accepted)
			$sql_add .= "l_accepted = '$l_accepted' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		if($l_login)
		{
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function load_by_id($l_id)
	{
		global $db;

		return $db->ExecuteSingle("SELECT * FROM logins WHERE l_id = $l_id");
	} // load_by_id

	function load_by_login($l_login)
	{
		global $db;

		return $this->load($l_login);
	} // load_by_login

	function load_by_login_ex($l_login)
	{
		global $db;

		return $this->load($l_login, '', LOGIN_ALL, LOGIN_ALL);
	} // load_by_login_ex

	function load_by_email($l_email)
	{
		global $db;

		return $db->ExecuteSingle("SELECT * FROM logins WHERE l_email = '$l_email'");
	} // load_by_email
/*
	function load_by_login($l_login)
	{
		global $db;

		return $db->ExecuteSingle("SELECT * FROM logins WHERE l_login = '$l_login'");
	} // load_by_login
*/
	function load_by_nick($l_nick)
	{
		global $db;

		return $db->ExecuteSingle("SELECT * FROM logins WHERE l_nick = '$l_nick'");
	} // load_by_nick

	function get_active()
	{
		global $db;
/*
		$dt = date('Y-m-d H:i:s', time() - 600);
		if($GLOBALS['i_am_admin'])
		{
			$sql = "SELECT * FROM logins WHERE l_logedin = 'Y' AND '$dt' < l_lastaccess";
			print $sql;
		} else {
			//$sql = "SELECT * FROM logins WHERE l_logedin = 'Y' AND NOW() - l_lastaccess < 600";
			$sql = "SELECT * FROM logins WHERE l_logedin = 'Y' AND NOW() - 600 < l_lastaccess";
			//$dt = time() - 600;
			//$sql = "SELECT * FROM logins WHERE l_logedin = 'Y' AND $dt < l_lastaccess";
		}
		*/
		//$sql = "SELECT * FROM logins WHERE l_logedin = 'Y' AND NOW() - 600 < l_lastaccess";
		$sql = sprintf(
			"SELECT * FROM logins WHERE l_logedin = 'Y' AND '%s' < l_lastaccess",
			date('Y-m-d H:i:s', time() - 600)
			);

		return $db->Execute($sql);
	} // get_active

	function save_session_data()
	{
		global $db;

		if(isset($_SESSION['login']))
		{
			$data = $_SESSION['login'];
			if(isset($data['l_id']) && ($data = $this->load_by_id($data['l_id'])))
			{
				$session_id = session_id();
				$l_id = $data['l_id'];
				$db->Execute("UPDATE logins SET l_sessiondata ='".session_encode()."', l_lastaccess = NOW(), l_logedin = 'Y' WHERE l_id = $l_id");
			}
		}
	} // save_session_data

	function set_profile(&$template, $login)
	{
		global $sys_user_root, $sys_user_http_root;

		$login['l_forumsort_themes'] = isset($login['l_forumsort_themes']) ? $login['l_forumsort_themes'] : FORUM_SORT_LAST_COMMENT;
		$login['l_forumsort_msg'] = isset($login['l_forumsort_msg']) ? $login['l_forumsort_msg'] : FORUM_SORT_ASC;
		$pic_localpath = $sys_user_root.'/pic/'.$login['l_id'].'.jpg';
		$tpic_localpath = $sys_user_root.'/pic/thumb/'.$login['l_id'].'.jpg';
		$pic_path = $sys_user_http_root.'/pic/'.$login['l_id'].'.jpg';
		$tpic_path = $sys_user_http_root.'/pic/thumb/'.$login['l_id'].'.jpg';

		//$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
		//$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';

		$template->enable('BLOCK_profile');
		$template->set_array($login);

		$template->set_var('l_forumsort_themes_'.$login['l_forumsort_themes'], ' checked="checked"');
		$template->set_var('l_forumsort_msg_'.$login['l_forumsort_msg'], ' checked="checked"');

		if(!empty($login['l_disable_avatars']))
		{
			$template->set_var('l_disable_avatars_checked', ' checked="checked"');
		} else {
			$template->set_var('l_disable_avatars_checked', '');
		}

		if($login['l_emailvisible'] != LOGIN_EMAIL_VISIBLE)
		{
			$template->set_var('l_emailvisible', '');
		} else {
			$template->set_var('l_emailvisible', ' checked="checked"');
		}

		if(file_exists($pic_localpath) && file_exists($tpic_localpath))
		{
			$template->set_var('pic_path', $tpic_path);
			if($info = getimagesize($pic_localpath))
			{
				$template->set_var('pic_w', $info[0]);
				$template->set_var('pic_h', $info[1]);
			} else {
				$template->set_var('pic_w', 400);
				$template->set_var('pic_h', 400);
			}

			$template->enable('BLOCK_picture');
		} else {
			$template->enable('BLOCK_nopicture');
		}
	} // set_profile

	function delete_image()
	{
		global $sys_user_root;

		$l_id = 0;
		if(isset($_SESSION['login']['l_id']) && $_SESSION['login']['l_id'])
		{
			$l_id = (int)$_SESSION['login']['l_id'];
		}

		if(!$l_id)
			return false;

		$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
		$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';

		return unlink($save_path) && unlink($tsave_path);
	} // delete_image

	function update_profile($data, $l_id = 0)
	{
		global $db, $sys_domain, $sys_user_root, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

		// check vai noraadiits id, vai ir ielogojies
		if(!$l_id)
		{
			$l_id_set = false;
			$l_id = 0;
			if(isset($_SESSION['login']['l_id']) && $_SESSION['login']['l_id'])
			{
				$l_id = (int)$_SESSION['login']['l_id'];
			}

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
		if($l_data = $this->load_by_id($l_id))
		{
			$this->validate($data);

			// check login status
			if($l_data['l_active'] != LOGIN_ACTIVE || $l_data['l_accepted'] != LOGIN_ACCEPTED)
			{
				$error_msg .= 'Nevar saglabāt neatktīvu profilu!<br />';
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
			$error_msg .= 'Nevar saglabāt neatktīvu kontu!<br />';
		}

		if(!$error_msg)
		{
			$sql = "INSERT INTO logins_change SELECT * FROM logins WHERE l_id = $l_id";
			$db->Execute($sql);

			$osql = $sql = '';
			$sql .= $data['l_email'] ? "l_email = '$data[l_email]', " : '';
			$sql .= $data['l_password'] ? "l_password = PASSWORD('$data[l_password]'), " : '';
			//$sql .= "l_emailvisible = '" .(isset($data['l_emailvisible']) ? LOGIN_EMAIL_VISIBLE : LOGIN_EMAIL_INVISIBLE)."', ";
			$sql .= "l_emailvisible = '$data[l_emailvisible]', ";
			$sql .= $data['l_forumsort_themes'] ? "l_forumsort_themes = '$data[l_forumsort_themes]', " : '';
			$sql .= $data['l_forumsort_msg'] ? "l_forumsort_msg = '$data[l_forumsort_msg]', " : '';
			$sql .= "l_disable_avatars = $data[l_disable_avatars], ";
			$osql .= $data['l_email'] ? "l_email = '$l_data[l_email]', " : '';
			$osql .= $data['l_password'] ? "l_password = '$l_data[l_password], " : '';

			// ja mainiits epasts, disable acc
			if($data['l_email'] && $data['l_email'] != $l_data['l_email'])
			{
				$sql .= "l_accepted = '".LOGIN_NOTACCEPTED."', ";
			}

			$sql = substr($sql, 0, -2);
			$osql = substr($osql, 0, -2);

			if($sql)
			{
				if($db->Execute("UPDATE logins SET $sql WHERE l_id = $l_id"))
				{
					// check new email changed
					if($data['l_email'] && ($data['l_email'] != $l_data['l_email']))
					{
						if($accept_code = $this->insert_accept_code($l_data['l_login']))
						{
							$msg = "Jusu epasts tika mainits!\n\nLudzu apstiprini jauno adresi, nospiezhot uz http://$sys_domain/register/accept/$accept_code/";
							if(!$this->send_accept_code($l_data['l_login'], $accept_code, $data['l_email'], 'truemetal.lv e-pasta apstiprinasana', $msg))
							{
								$this->accept_login($accept_code);
								// rollback (god damn, mehehehheee)
								$db->Execute("UPDATE logins SET $osql WHERE l_id = $l_id");
								$this->error_msg = 'Nevar nosutit kodu uz "'.$data['l_email'].'"<br />('.$GLOBALS['php_errormsg'].')';
								return false;
							}
						}
					} // check email

					if($_FILES['l_picfile']['tmp_name'])
					{
						// handle image
						$save_path = $sys_user_root.'/pic/'.$l_id.'.jpg';
						$tsave_path = $sys_user_root.'/pic/thumb/'.$l_id.'.jpg';
						if($ct = save_file('l_picfile', $save_path))
						{
						// ja bilde
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

							return $this->load_by_id($l_id);
						} else {
							$this->error_msg = 'Nevar saglabāt failu ['.$_FILES['l_picfile']['name'].']';
							return false;
						} // save file
					} // $_FILES

					return $this->load_by_id($l_id);
				} // execute
			} // sql

		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // update_profile

	function login($l_login = '', $l_pass = '')
	{
		global $db;

		if($this->valid_login($l_login) && $l_pass && ($data = $this->load($l_login, $l_pass)))
		{
			$db->Execute("UPDATE logins SET l_logedin ='Y' WHERE l_id = $data[l_id]");
			return $data;
		} else {
			$this->error_msg = 'Nepareizs login vai parole!';
			return array();
		}

	} // login

	function logoff()
	{
		global $db;
		if(isset($_SESSION['login']))
		{
			$data = $_SESSION['login'];
			if(isset($data['l_id']) && ($data = $this->load_by_id($data['l_id'])))
			{
				$db->Execute("UPDATE logins SET l_logedin ='N' WHERE l_id = $data[l_id]");
				$this->save_session_data();
				session_destroy();

				return true;
			}
		}
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
		global $sys_domain, $http_root, $db;

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
		global $sys_domain, $http_root, $db;

		if(!$msg)
		{
			$msg = "Veiksmiga registracija!\n\nLudzu apstiprini savu registraciju, nospiezhot uz http://$sys_domain/register/accept/$code/";
		}

		if(!$subj)
		{
			$subj = 'truemetal.lv registracija';
		}

		if(email($email, $subj, $msg))
		{
			$sql = "UPDATE login_accept SET la_sent = 'Y' WHERE la_login = '$login'";
			return $db->Execute($sql);
		} else
			return false;

	} // insert_accept_code

	function accept_login($code, $timeout = 259200) // 3*24h
	{
		global $db;

		$sql = "SELECT * FROM login_accept WHERE la_code = '$code' AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(la_entered) < $timeout AND la_accepted = '0000-00-00 00:00:00'";

		if($data = $db->ExecuteSingle($sql))
		{
			$sql = "UPDATE login_accept SET la_accepted = NOW() WHERE la_login = '$data[la_login]'";
			$db->Execute($sql);
			$sql = "UPDATE logins SET l_accepted = '".LOGIN_ACCEPTED."' WHERE l_login = '$data[la_login]'";
			if($db->Execute($sql))
				return true;
		}

		return false;
	} // load_accept_code

	function get_forgot($code, $timeout = 259200) // 3*24h
	{
		global $db;

		$sql = "SELECT * FROM login_forgot WHERE f_code = '$code' AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(f_entered) < $timeout";

		return $db->ExecuteSingle($sql);
	} // get_forgot

	function insert(&$data, $validate = LOGIN_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

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

/*
	function update($l_login, &$data, $validate = LOGIN_VALIDATE)
	{
		global $db;

		if(!$this->valid_login($l_login))
		{
			$this->error_msg = 'Nav norādīts vai nepareizs lietotāja logins<br />';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE logins SET ';
		$sql .= $data['l_entered'] ? "l_entered = '$data[l_entered]', " : '';
		$sql .= "l_password = PASSWORD('$data[l_password]'), ";
		$sql .= "l_firstname = '$data[l_firstname]', ";
		$sql .= "l_lastname = '$data[l_lastname]', ";
		$sql .= "l_phone = '$data[l_phone]', ";
		$sql .= "l_email = '$data[l_email]', ";
		$sql .= "l_birth = '$data[l_birth]', ";
		$sql .= "l_type = $data[l_type], ";
		$sql .= "l_spec = $data[l_spec], ";
		$sql .= "l_sertnr = '$data[l_sertnr]', ";
		$sql .= "l_sertexpire = '$data[l_sertexpire]', ";
		$sql .= "l_active = '$data[l_active]', ";
		$sql .= "l_accepted = '$data[l_accepted]', ";
		$sql = substr($sql, 0, -2);
		$sql .= " WHERE l_login = '$l_login'";

		$db->Execute($sql);

		return $l_login;
	} // update

	function save($l_login, &$data)
	{
		$this->validate($data);

		$error_msg = '';

		if(!$data['l_login'])
			$error_msg .= 'Nav norādīts lietotāja logins<br />';

		if(!$data['l_firstname'] || !$data['l_lastname'])
			$error_msg .= 'Nav norādīts lietotāja vārds/uzvārds<br />';

		if(!$error_msg)
		{
			if($l_login)
				return $this->update($l_login, $data, LOGIN_DONTVALIDATE);
			else
				return $this->insert($data, LOGIN_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save
*/

	function del($l_id)
	{
		global $db;

		if(!$l_id)
			return true;

		$sql = "DELETE FROM logins WHERE l_id = '$l_id'";

		return $db->Execute($sql);
	} // del

	function activate($l_id)
	{
		global $db;

		$sql = 'UPDATE logins SET l_active = "'.LOGIN_ACTIVE.'" WHERE l_id = "'.$l_id.'"';

		return $db->Execute($sql);
	} // activate

	function deactivate($l_id)
	{
		global $db;

		$sql = 'UPDATE logins SET l_active = "'.LOGIN_INACTIVE.'" WHERE l_id = "'.$l_id.'"';

		return $db->Execute($sql);
	} // deactivate

	function accept($l_id)
	{
		global $db;

		if($l = $this->load_by_id($l_id) && $l['l_accepted'] != LOGIN_ACCEPTED) {
			$sql = 'UPDATE logins SET l_accepted = "'.LOGIN_ACCEPTED.'" WHERE l_id = "'.$l_id.'"';
			if($db->Execute($sql)) {
				$this->email_accept($l_id);
				return true;
			} else {
				return false;
			}
		} else // load
			return false;
	} // accept

	function email_accept($l_id)
	{
		if($l = $this->load_by_id($l_id))
			email($l['l_email'], 'lpa.lv accept', "Registracija akcepteta!\n\nwww.lpa.lv");
	} // email_accept

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
			$data['l_active'] = ereg('[^YN]', $data['l_active']) ? '' : $data['l_active'];
		else
			$data['l_active'] = LOGIN_ACTIVE;

		if(isset($data['l_emailvisible']))
			$data['l_emailvisible'] = LOGIN_EMAIL_VISIBLE;
		else
			$data['l_emailvisible'] = LOGIN_EMAIL_INVISIBLE;

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
			$data['l_type'] = !ereg('[0-9]', $data['l_type']) ? 0 : $data['l_type'];
		else
			$data['l_type'] = 0;

		if(isset($data['l_spec']))
			$data['l_spec'] = !ereg('[0-9]', $data['l_spec']) ? 0 : $data['l_spec'];
		else
			$data['l_spec'] = 0;

		if(!isset($data['l_sertnr']))
			$data['l_sertnr'] = '';

		if(!isset($data['l_sertexpire']))
			$data['l_sertexpire'] = '';

		if(!isset($data['l_entered']))
			$data['l_entered'] = '';

		if(isset($data['l_accepted']))
			$data['l_accepted'] = ereg('[^YN]', $data['l_accepted']) ? '' : $data['l_accepted'];
		else
			$data['l_accepted'] = LOGIN_NOTACCEPTED;

		if(isset($data['l_forumsort_themes']))
			$data['l_forumsort_themes'] = ereg('[^TC]', $data['l_forumsort_themes']) ? '' : $data['l_forumsort_themes'];
		else
			$data['l_forumsort_themes'] = FORUM_SORT_THEME;

		if(isset($data['l_forumsort_msg']))
			$data['l_forumsort_msg'] = ereg('[^AD]', $data['l_forumsort_msg']) ? '' : $data['l_forumsort_msg'];
		else
			$data['l_forumsort_msg'] = FORUM_SORT_THEME;

		if(isset($data['l_disable_avatars']))
			$data['l_disable_avatars'] = 1;
		else
			$data['l_disable_avatars'] = 0;

	} // validate

	function valid_login($user_login)
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

}

