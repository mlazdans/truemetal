<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../classes/class.Admin.php');

define('BAN_ALL', false);
define('BAN_ACTIVE', 'Y');
define('BAN_INACTIVE', 'N');
define('BAN_VALIDATE', true);
define('BAN_DONTVALIDATE', false);

class Ban extends Admin
{
	var $error_msg;
	var $permissions;

	function Ban()
	{
		Admin::Admin('ban');
		if(!$this->CheckPermissions('admin'))
			return false;
	} // Ban

	function _convert($item)
	{
		if(isset($item['ub_net']) && isset($item['ub_mask'])) {
			$item['ub_net'] = long2ip($item['ub_net']);
			$item['ub_mask'] = long2ip($item['ub_mask']);
		}

		return $item;
	} // _convert

	function convert($data)
	{
		if(is_array($data))
			foreach($data as $k=>$v)
				$data[$k] = $this->_convert($v);

		return $data;
	} // convert

	function load($ub_id = 0, $ub_moduleid = '', $ub_active = BAN_ACTIVE)
	{
		global $db;

		$ub_id = (integer)$ub_id;

		$sql_add = '';
		$sql = 'SELECT * FROM user_bans';

		if($ub_id)
			$sql_add .= "ub_id = $ub_id AND ";

		if($ub_moduleid)
			$sql_add .= "ub_moduleid = '$ub_moduleid' AND ";

		if($ub_active)
			$sql_add .= "ub_active = '$ub_active' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' ORDER BY ub_entered DESC';

		if($ub_id) {
			if($data = $db->ExecuteSingle($sql))
				return $this->_convert($data);
			else
				return $data;
		} else
			if($data = $db->Execute($sql))
				return $this->convert($data);
			else
				return $data;
	} // load

	function banned($ub_ip, $ub_moduleid, $ub_active = BAN_ACTIVE)
	{
		global $db;

		$ub_ip = sprintf("%u", ip2long($ub_ip));

		$sql_add = '(NOW() BETWEEN ub_entered AND ub_expires) AND ';
		$sql = "SELECT * FROM user_bans";

		if(!$ub_ip)
			return false;
		else
			$sql_add .= "(ub_net = $ub_ip & ub_mask) AND ";

		if($ub_moduleid)
			$sql_add .= "ub_moduleid = '$ub_moduleid' AND ";

		if($ub_active)
			$sql_add .= "ub_active = '$ub_active' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$data = $db->ExecuteSingle($sql);
		if(count($data))
			return $data;
		else
			return false;
	} // banned

	function insert(&$data, $validate = BAN_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$sql = "
		SELECT
			*
		FROM
			user_bans
		WHERE
			ub_net = '$data[ub_net]' AND ub_mask = '$data[ub_mask]' AND
			ub_moduleid = '$data[ub_moduleid]'";

		$ban = $db->ExecuteSingle($sql);
		if(count($ban)) {
			$ban['ub_active'] = BAN_ACTIVE;
			$this->update($ban['ub_id'], $ban);
			return $ban['ub_id'];
		}

		$sql = "
		INSERT INTO user_bans (
			ub_moduleid, ub_net, ub_mask,
			ub_active, ub_reason, ub_entered, ub_expires
		) VALUES (
			'$data[ub_moduleid]', $data[ub_net], $data[ub_mask],
			'$data[ub_active]', '$data[ub_reason]', $data[ub_entered],
			$data[ub_expires]
		)";

		if($db->Execute($sql))
			return last_insert_id();
		else
			return false;
	} // insert

	function update($ub_id, &$data, $validate = BAN_VALIDATE)
	{
		global $db;

		$ub_id = (integer)$ub_id;

		if(!$ub_id) {
			$this->error_msg = 'Nav norādīts vai nepareizs bana ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE user_bans SET ';
		$sql .= "ub_moduleid = '$data[ub_moduleid]', ";
		$sql .= "ub_net = '$data[ub_net]', ";
		$sql .= "ub_mask = '$data[ub_mask]', ";
		$sql .= "ub_active = '$data[ub_active]', ";
		$sql .= "ub_reason = '$data[ub_reason]', ";
		$sql .= "ub_entered = $data[ub_entered], ";
		$sql .= "ub_expires = $data[ub_expires], ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE ub_id = '.$ub_id;

		$db->Execute($sql);
		return $ub_id;
	} // update

	function unban($ub_ip, $ub_moduleid)
	{
		global $db;

		if(!$this->CheckPermissions(array('admin')))
			return false;

		$ub_ip = sprintf("%u", ip2long($ub_ip));

		$sql = "UPDATE user_bans SET ub_active = '".BAN_INACTIVE."'
			WHERE (ub_net = $ub_ip & ub_mask) AND ub_moduleid = '$ub_moduleid'";

		return $db->Execute($sql);
	} // unban

	function save(&$data)
	{
		if(!$this->CheckPermissions('admin'))
			return false;

		$this->validate($data);

		$error_msg = '';

		if(!$data['ub_moduleid'])
			$error_msg .= 'Nav norādīts modulis<br>';

		if(!$data['ub_net'])
			$error_msg .= 'Nav norādīta ip adrese/tīkls<br>';

		if(!$error_msg) {
			if($data['ub_id'])
				return $this->update($data['ub_id'], $data, BAN_DONTVALIDATE);
			else
				return $this->insert($data, BAN_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save

	function del($ub_id)
	{
		global $db;

		if(!$this->CheckPermissions('admin'))
			return false;

		$ub_id = (integer)$ub_id;

		if(!$ub_id)
			return true;

		$sql = 'DELETE FROM user_bans WHERE ub_id = '.$ub_id;

		return $db->Execute($sql);
	} // del

	function activate($ub_id)
	{
		global $db;

		if(!$this->CheckPermissions('admin'))
			return false;

		$ub_id = (integer)$ub_id;

		$sql = 'UPDATE user_bans SET ub_active = "'.BAN_ACTIVE.'" WHERE ub_id = '.$ub_id;

		return $db->Execute($sql);
	} // activate

	function deactivate($ub_id)
	{
		global $db;

		if(!$this->CheckPermissions('admin'))
			return false;

		$ub_id = (integer)$ub_id;

		$sql = 'UPDATE user_bans SET ub_active = "'.BAN_INACTIVE.'" WHERE ub_id = '.$ub_id;

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

		if(isset($data['ub_count']) && $func)
			for($r = 1; $r <= $data['ub_count']; ++$r) {
				// ja iechekots, proceseejam
				if(isset($data['ub_checked'.$r]) && isset($data['ub_id'.$r]))
					$ret = $ret && $this->{$func}($data['ub_id'.$r]);
			}

		return $ret;
	} // process_action

	function set_up_modules(&$template)
	{
		$module_list = get_modules();

		if(count($module_list)) {
			$template->reset_block('BLOCK_module_list');
			$template->enable('BLOCK_module_list');
			foreach($module_list as $module) {
				$template->set_var('module', $module);
				$template->parse_block('BLOCK_module_list', TMPL_APPEND);
			}
		}
	} // set_up_modules

	function validate(&$data)
	{
		global $db;

		if(isset($data['ub_active']))
			$data['ub_active'] = ereg('[^YN]', $data['ub_active']) ? '' : $data['ub_active'];
		else
			$data['ub_active'] = BAN_ACTIVE;

		if(isset($data['ub_id']))
			$data['ub_id'] = !ereg('[0-9]', $data['ub_id']) ? 0 : $data['ub_id'];
		else
			$data['ub_id'] = 0;

		if(!isset($data['ub_moduleid']))
			$data['ub_moduleid'] = '';

		if(!isset($data['ub_net']))
			$data['ub_net'] = 0;
		else
			$data['ub_net'] = sprintf("%u", ip2long($data['ub_net']));

		if(!isset($data['ub_mask']))
			$data['ub_mask'] = sprintf("%u", ip2long('255.255.255.255'));
		else
			$data['ub_mask'] = sprintf("%u", ip2long($data['ub_mask']));

		if(!isset($data['ub_reason']))
			$data['ub_reason'] = 'No reason';

		if(!isset($data['ub_entered']) || !$data['ub_entered'])
			$data['ub_entered'] = $db->now();
		else
			$data['ub_entered'] = "'$data[ub_entered]'";

		if(!isset($data['ub_expires']) || !$data['ub_expires'])
			$data['ub_expires'] = 'DATE_ADD('.$data['ub_entered'].', INTERVAL 2 DAY)';
		else
			$data['ub_expires'] = "'$data[ub_expires]'";

	} // validate

} // Ban
