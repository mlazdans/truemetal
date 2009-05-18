<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../classes/class.User.php');

define('UP_VALIDATE', true);
define('UP_DONTVALIDATE', false);

class Permission
{
	var $error_msg, $permissions;

	function Permission()
	{
		global $_USER;

		$this->permissions = $this->user_permissions($_USER['user_login'], 'permission');
		if(!in_array('admin', $this->permissions)) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}
	} // Permission

	function load_users($user_login = '', $user_active = USER_ACTIVE)
	{
		global $db;

		$sql_add = 'user_login = up_userlogin AND ';
		$sql = 'SELECT user_login, user_name FROM user_permissions, user';

		if($user_active)
			$sql_add .= "user_active = '$user_active' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' GROUP BY user_login, user_name';
		$sql .= ' ORDER BY up_entered';

		return $db->Execute($sql);
	} // load_users

	function load($up_id = 0, $user_login = '', $user_active = USER_ACTIVE)
	{
		global $db;

		$up_id = (integer)$up_id;

		$sql_add = 'user_login = up_userlogin AND ';
		$sql = 'SELECT * FROM user_permissions, user';

		if($up_id)
			 $sql_add .= "up_id = $up_id AND ";

		if($user_active)
			$sql_add .= "user_active = '$user_active' AND ";

		if($user_login)
			$sql_add .= "user_login = '$user_login' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' ORDER BY user_login, up_entered';

		if($up_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function insert(&$data, $validate = UP_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$date = $db->now();
		if($data['up_entered'])
			$date = "'$data[up_entered]'";

		$sql = "
		INSERT INTO user_permissions (
			up_userlogin, up_moduleid, up_permissions, up_entered
		) VALUES (
			'$data[up_userlogin]', '$data[up_moduleid]', '$data[up_permissions]', $date
		)";

		if($db->Execute($sql))
			return $data['up_userlogin'];
		else
			return false;
	} // insert

	function update($up_id, &$data, $validate = UP_VALIDATE)
	{
		global $db;

		$up_id = (integer)$up_id;

		if(!$up_id) {
			$this->error_msg = 'Nav norādīts vai nepareizs lietotāja tiesību ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE user_permissions SET ';
		$sql .= $data['up_entered'] ? "up_entered = '$data[up_entered]', " : '';
		$sql .= "up_userlogin = '$data[up_userlogin]', ";
		$sql .= "up_moduleid = '$data[up_moduleid]', ";
		$sql .= "up_permissions = '$data[up_permissions]', ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE up_id = '.$up_id;

		$db->Execute($sql);
		return $data['up_userlogin'];
	} // update

	function save(&$data)
	{
		$this->validate($data);

		$error_msg = '';

		if(!$data['up_userlogin'])
			$error_msg .= 'Nav norādīts lietotājs<br>';

		if(!$data['up_moduleid'])
			$error_msg .= 'Nav norādīts modulis<br>';

		if(!$error_msg) {
			if($data['up_id'])
				return $this->update($data['up_id'], $data, UP_DONTVALIDATE);
			else
				return $this->insert($data, UP_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save

	function del($up_id = 0, $up_userlogin = '')
	{
		global $db;

		$up_id = (integer)$up_id;

		if(!$up_id && !$up_userlogin)
			return true;

		if($up_id)
			$sql = 'DELETE FROM user_permissions WHERE up_id = '.$up_id;
		elseif($up_userlogin)
			$sql = "DELETE FROM user_permissions WHERE up_userlogin = '$up_userlogin'";

		return $db->Execute($sql);
	} // del

	function del_bylogins($data)
	{
		$ret = true;

		if(isset($data['up_count']))
			for($r = 1; $r <= $data['up_count']; ++$r) {
				// ja iechekots, proceseejam
				if(isset($data['up_checked'.$r]) && isset($data['up_login'.$r]))
					$ret = $ret && $this->del(0, $data['up_login'.$r]);
			}

		return $ret;
	} // del_bylogins

	function process_action(&$post, $action)
	{
		$ret = true;
		$func = '';

		if($action == 'delete_multiple')
			$func = 'del';

		if($action == 'save_multiple')
			$func = 'update';

		if(isset($post['up_count']) && $func)
			for($r = 1; $r <= $post['up_count']; ++$r) {
				$data = $post['data'.$r];
				// ja iechekots, proceseejam
				if(isset($data['up_checked']) && isset($data['up_id'])) {
					if($func == 'update')
						$ret = $ret && $this->{$func}($data['up_id'], $data);
					else
						$ret = $ret && $this->{$func}($data['up_id']);
				}
			}

		return $ret;
	} // process_action

	function get_permissions()
	{
		global $db;

		$sql = "SHOW COLUMNS FROM user_permissions LIKE 'up_permissions'";
		$data = $db->ExecuteSingle($sql);

		preg_match_all("/'(.*)'/U", $data['Type'], $m);

		return $m[1];
	} // get_permissions

	function set_up_permissions(&$template, $up_permissions = '')
	{
		$up_list = $this->get_permissions();
		$up_permissions = split(',', $up_permissions);

		if(is_array($up_list)) {
			$template->reset_block('BLOCK_perm_list');
			$template->set_var('perm_list_size', count($up_list));
			$template->enable('BLOCK_perm_list');
			foreach($up_list as $perm) {
				if(in_array($perm, $up_permissions))
					$template->set_var('perm_selected', ' selected');
				else
					$template->set_var('perm_selected', '');

				$template->set_var('perm', $perm);
				$template->parse_block('BLOCK_perm_list', TMPL_APPEND);
			}
		}
	} // set_up_permissions

	function set_up_modules(&$template)
	{
		$module_list = get_modules(true);

		if(count($module_list)) {
			$template->reset_block('BLOCK_module_list');
			$template->enable('BLOCK_module_list');
			foreach($module_list as $module) {
				$template->set_var('module', $module);
				$template->parse_block('BLOCK_module_list', TMPL_APPEND);
			}
		}
	} // set_up_modules

	function set_up_users(&$template, $user_login = '')
	{
		$user = new User;
		$users = $user->load();

		if(count($users)) {
			$template->enable('BLOCK_user_list');
			foreach($users as $item) {
				if($user_login && ($user_login == $item['user_login']))
					$template->set_var('user_selected', ' selected');
				else
					$template->set_var('user_selected', '');

				$template->set_var('user_login', $item['user_login']);
				$template->set_var('user_name', $item['user_name']);
				$template->parse_block('BLOCK_user_list', TMPL_APPEND);
			}
		}
	} // set_up_users

	function validate(&$data)
	{
		if(isset($data['up_id']))
			$data['up_id'] = !ereg('[0-9]', $data['up_id']) ? 0 : $data['up_id'];
		else
			$data['up_id'] = 0;

		if(!isset($data['up_userlogin']))
			$data['up_userlogin'] = '';

		if(!isset($data['up_moduleid']))
			$data['up_moduleid'] = '';

		if(!isset($data['up_permissions']))
			$data['up_permissions'] = array();

		$perm = '';
		foreach($data['up_permissions'] as $item)
			$perm .= "$item,";
		$perm = substr($perm, 0, -1);
		$data['up_permissions'] = $perm;

		if(!isset($data['up_entered']))
			$data['up_entered'] = '';
	} // validate

	function user_permissions($up_userlogin, $up_moduleid)
	{
		global $db;

		$up_userlogin = addslashes($up_userlogin);
		$up_moduleid = addslashes($up_moduleid);

		$sql = "
		SELECT
			up_permissions
		FROM
			user_permissions
		WHERE
			up_userlogin = '$up_userlogin' AND
			up_moduleid = '$up_moduleid'";

		$ret = array();
		$data = $db->Execute($sql);
		foreach($data as $item)
			$ret = array_merge($ret, split(',', $item['up_permissions']));

		return array_unique($ret);
	} // user_permissions

} // Permission
