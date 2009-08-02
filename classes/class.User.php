<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../includes/inc.utils.php');

define('USER_ACTIVE', 'Y');
define('USER_INACTIVE', 'N');
define('USER_ALL', false);
define('USER_VALIDATE', true);
define('USER_DONTVALIDATE', false);

class User
{
	var $error_msg;
	var $data;
	var $logged_id;

	function __construct()
	{
	} // __construct

	function login($user_login = '', $user_pass = '')
	{
		if($this->valid_login($user_login) && $user_pass) {
			$this->data = $this->load($user_login, $user_pass);
			$this->logged_id = true;
			return true;
		} else {
			$this->data = array();
			$this->logged_id = false;
			$this->error_msg = 'Nepareizs login vai parole!';
			return false;
		}

	} // login

	function load($user_login = '', $user_pass = '', $user_active = USER_ACTIVE)
	{
		global $db;

		$sql_add = '';
		$sql = 'SELECT * FROM user';

		if($this->valid_login($user_login))
			$sql_add .= "user_login='$user_login' AND ";

		if($user_pass)
			$sql_add .= "user_pass='$user_pass' AND ";

		if($user_active)
			$sql_add .= "user_active = '$user_active' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' ORDER BY user_entered';

		if($user_login) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function insert(&$data, $validate = USER_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$date = $db->now();
		if($data['user_entered'])
			$date = "'$data[user_entered]'";

		$sql = "
		INSERT INTO user (
			user_login, user_pass, user_name,
			user_email, user_homepage,
			user_active, user_entered
		) VALUES (
			'$data[user_login]', '$data[user_pass]', '$data[user_name]',
			'$data[user_email]', '$data[user_homepage]',
			'$data[user_active]', $date
		)";

		if($db->Execute($sql))
			return $data['user_login'];
		else
			return false;
	}

	function update($user_login, &$data, $validate = USER_VALIDATE)
	{
		global $db;

		if(!$this->valid_login($user_login)) {
			$this->error_msg = 'Nav norādīts vai nepareizs lietotāja logins<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE user SET ';
		$sql .= $data['user_entered'] ? "user_entered = '$data[user_entered]', " : '';
		//$sql .= "user_login = '$data[user_login]', ";
		$sql .= "user_pass = '$data[user_pass]', ";
		$sql .= "user_name = '$data[user_name]', ";
		$sql .= "user_email = '$data[user_email]', ";
		$sql .= "user_homepage = '$data[user_homepage]', ";
		$sql .= "user_active = '$data[user_active]', ";
		$sql = substr($sql, 0, -2);
		$sql .= " WHERE user_login = '$user_login'";

		$db->Execute($sql);

		return $user_login;
	} // update

	function save($user_login, &$data)
	{
		$this->validate($data);

		$error_msg = '';

		if(!$data['user_login'])
			$error_msg .= 'Nav norādīts lietotāja logins<br>';

		if(!$data['user_name'])
			$error_msg .= 'Nav norādīts lietotāja nosaukums<br>';

		if(!$error_msg) {
			if($user_login)
				return $this->update($user_login, $data, USER_DONTVALIDATE);
			else
				return $this->insert($data, USER_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save

	function del($user_login)
	{
		global $db;

		if(!$user_login)
			return true;

		$sql = "DELETE FROM user WHERE user_login = '$user_login'";

		return $db->Execute($sql);
	} // del

	function activate($user_login)
	{
		global $db;

		$sql = 'UPDATE user SET user_active = "'.USER_ACTIVE.'" WHERE user_login = "'.$user_login.'"';

		return $db->Execute($sql);
	} // activate

	function deactivate($user_login)
	{
		global $db;

		$sql = 'UPDATE user SET user_active = "'.USER_INACTIVE.'" WHERE user_login = "'.$user_login.'"';

		return $db->Execute($sql);
	} // deactivate

	# actionu preprocessors
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

		if(isset($data['user_count']) && $func)
			for($r = 1; $r <= $data['user_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['user_checked'.$r]) && isset($data['user_login'.$r]))
					$ret = $ret && $this->{$func}($data['user_login'.$r]);

		return $ret;
	} // process_action

	function validate(&$data)
	{
		if(isset($data['user_active']))
			$data['user_active'] = ereg('[^YN]', $data['user_active']) ? '' : $data['user_active'];
		else
			$data['user_active'] = USER_ACTIVE;

		if(!isset($data['user_login']))
			$data['user_login'] = '';

		if(!isset($data['user_pass']))
			$data['user_pass'] = '';

		if(!isset($data['user_name']))
			$data['user_name'] = '';

		if(!isset($data['user_email']))
			$data['user_email'] = '';

		if(!isset($data['user_homepage']))
			$data['user_homepage'] = '';

		if(!isset($data['user_entered']))
			$data['user_entered'] = '';
	} // validate

	function valid_login($user_login)
	{
		return valid($user_login) && (strlen($user_login) > 0);
	} // valid_login

} // class User

