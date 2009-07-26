<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//
define('MOD_ALL', -1);
define('MOD_NONE', -2);
define('MOD_ACTIVE', 'Y');
define('MOD_INACTIVE', 'N');
define('MOD_VISIBLE', 'Y');
define('MOD_INVISIBLE', 'N');
define('MOD_TYPE_OPEN', 'O');
define('MOD_TYPE_REGISTRATED', 'R');

class Module
{
	var $data = array();
	var $error_msg;
	var $primary_key;

	function Module()
	{
		$this->primary_key = 'mod_id';
	} // Module

	function load($mod_id = 0)
	{
		global $db, $sys_lang;

		if($mod_id)
			$where = ' WHERE mod_id = '.$mod_id;
		else
			$where = '';

		$this->data = array();
		$sql = 'SELECT * FROM modules_'.$sys_lang.$where.' ORDER BY mod_modid, module_pos';
		$data = $db->execute($sql);
		$this->data = array();
		foreach($data as $item)
			$this->data[$item[$this->primary_key]] = $item;
	} // load

	function get_item($key)
	{
		if(!isset($this->data) || !count($this->data))
			$this->load($key);

		return isset($this->data[$key]) ? $this->data[$key] : false;
	} // get_item

	function insert(&$data)
	{
		global $db, $sys_lang;

		$sql = "INSERT INTO modules_$sys_lang (".
			"module_id, mod_modid, module_name,".
			"module_active, module_pos, module_data, module_entered,".
			"module_visible, module_type".
		") VALUES (".
			"'$data[module_id]', $data[mod_modid], '$data[module_name]',".
			"'$data[module_active]', $data[module_pos], '$data[editor_data]', NOW(),".
			"'$data[module_visible]', '$data[module_type]'".
		")";

		$ret = $db->Execute($sql);

		if($ret) {
			$last_id = $db->LastID();
			$sql = "UPDATE modules_$sys_lang SET ".
				"module_pos = module_pos + 1 ".
				"WHERE ".
				"module_pos >= $data[module_pos] AND ".
				"mod_id != $last_id AND ".
				"mod_modid = $data[mod_modid]";
			$db->execute($sql);
			return $last_id;
		}

		return false;
	} // insert

	function update(&$data)
	{
		global $db, $sys_lang;

		$data2 = $this->get_item($data['mod_id']);

		$sql = "UPDATE modules_$sys_lang SET ".
			"module_id = '$data[module_id]', module_name = '$data[module_name]',".
			"module_active = '$data[module_active]', module_pos = $data[module_pos],".
			"module_data = '$data[editor_data]',".
			"module_visible = '$data[module_visible]',".
			"module_type = '$data[module_type]'".
		"WHERE ".
			"mod_id = $data[mod_id]";

		$ret = $db->execute($sql);

		if($ret) {
			$sql = '';
			if($data['module_pos'] > $data2['module_pos'])
				$sql = "UPDATE modules_$sys_lang SET ".
					"module_pos = module_pos - 1 ".
					"WHERE ".
					"module_pos >= $data2[module_pos] AND ".
					"module_pos <= $data[module_pos] AND ".
					"mod_id != $data[mod_id] AND ".
					"mod_modid = $data2[mod_modid]";
			elseif($data['module_pos'] < $data2['module_pos'])
				$sql = "UPDATE modules_$sys_lang SET ".
					"module_pos = module_pos + 1 ".
					"WHERE ".
					"module_pos <= $data2[module_pos] AND ".
					"module_pos >= $data[module_pos] AND ".
					"mod_id != $data[mod_id] AND ".
					"mod_modid = $data2[mod_modid]";
			if($sql)
				$db->execute($sql);
			return $data['mod_id'];
		}

		return false;
	} // update

	function save(&$data) {
		$this->validate($data);

		$error_msg = '';
		if(!$data['module_id'])
			$error_msg .= 'Nav norādīts vai nepareizs moduļa ID<br>';
		if(!$data['module_name'])
			$error_msg .= 'Nav norādīts moduļa nosaukums<br>';

		if(!$error_msg) {
			if($data['mod_id'])
				return $this->update($data);
			else
				return $this->insert($data);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save

	function validate(&$data)
	{
		$data['module_id'] = ereg('[^a-z0-9_]', $data['module_id']) ? '' : $data['module_id'];
		$data['mod_id'] = !ereg('[0-9]', $data['mod_id']) ? '' : $data['mod_id'];
		$data['module_pos'] = !ereg('[0-9]', $data['module_pos']) ? 0 : $data['module_pos'];
		$data['module_active'] = ereg('[^YN]', $data['module_active']) ? '' : $data['module_active'];
		$data['module_visible'] = ereg('[^YN]', $data['module_visible']) ? '' : $data['module_visible'];
		$data['module_type'] = ereg('[^OR]', $data['module_type']) ? '' : $data['module_type'];
	} // validate

	/*
	function del($mod_id) {
		global $db;

		$mod_id = (integer)$mod_id;
		$data = $this->get_item($mod_id);

		$sql = "DELETE FROM modules WHERE mod_id = $mod_id";
		$ret = $db->execute($sql);

		if($ret) {
			$sql = "UPDATE modules SET ".
				"module_pos = module_pos - 1 ".
				"WHERE ".
				"module_pos > $data[module_pos] AND ".
				"mod_modid = $data[mod_modid]";
			$db->execute($sql);
		}

		return $ret;
	}
	*/

	function del_under($mod_id)
	{
		global $db, $sys_lang;

		$mod_id = (integer)$mod_id;

		if(!$mod_id)
			return true;

		$ret = true;

		$sql = "SELECT mod_id FROM modules_$sys_lang WHERE mod_modid = ".$mod_id;
		$data = $db->Execute($sql);
		foreach($data as $item)
			$ret = $ret && $this->del($item['mod_id']);

		$sql = "DELETE FROM modules_$sys_lang WHERE mod_modid = ".$mod_id;

		return $ret && $db->Execute($sql);
	} // del_under

	function del($mod_id)
	{
		global $db, $sys_lang;

		$mod_id = (integer)$mod_id;
		$data = $this->get_item($mod_id);

		if(!$mod_id)
			return true;

		$ret = $this->del_under($mod_id);

		$sql = "DELETE FROM modules_$sys_lang WHERE mod_id = $mod_id";
		$ret2 = $db->Execute($sql);

		if($ret2) {
			$sql = "UPDATE modules_$sys_lang SET ".
				"module_pos = module_pos - 1 ".
				"WHERE ".
				"module_pos > $data[module_pos] AND ".
				"mod_modid = $data[mod_modid]";
			$db->execute($sql);
		}

		return $ret && $ret2;
	} // del

	function activate($mod_id)
	{
		global $db, $sys_lang;

		$mod_id = (integer)$mod_id;
		$sql = "UPDATE modules_$sys_lang SET module_active = '".MOD_ACTIVE."' WHERE mod_id = $mod_id";

		return $db->Execute($sql);
	} // activate

	function deactivate($mod_id)
	{
		global $db, $sys_lang;

		$mod_id = (integer)$mod_id;
		$sql = "UPDATE modules_$sys_lang SET module_active = '".MOD_INACTIVE."' WHERE mod_id = $mod_id";

		return $db->Execute($sql);
	} // deactivate

	function show($mod_id)
	{
		global $db, $sys_lang;

		$mod_id = (integer)$mod_id;
		$sql = "UPDATE modules_$sys_lang SET module_visible = '".MOD_VISIBLE."' WHERE mod_id = $mod_id";

		return $db->Execute($sql);
	} // show

	function hide($mod_id)
	{
		global $db, $sys_lang;

		$mod_id = (integer)$mod_id;
		$sql = "UPDATE modules_$sys_lang SET module_visible = '".MOD_INVISIBLE."' WHERE mod_id = $mod_id";

		return $db->Execute($sql);
	} // hide

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

		if($action == 'show_multiple')
			$func = 'show';

		if($action == 'hide_multiple')
			$func = 'hide';

		if(isset($data['item_count']) && $func)
			for($r = 1; $r <= $data['item_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['mod_checked'.$r]) && isset($data['mod_id'.$r]))
					$ret = $ret && $this->{$func}($data['mod_id'.$r]);

		return $ret;
	} // process_action

	function load_tree($mod_modid = 0, $q = '', $registrated = false)
	{
		global $db, $sys_lang;

		$match = '';
		if($q)
			$match = ', '.search_to_sql($q, array('module_name', 'module_data')).' score';
			//$match = ",(module_name REGEXP '$q' OR module_data REGEXP '$q') score";
			//$match = ",MATCH(module_name, module_data) AGAINST('$q') score";

		$sql = "SELECT m.*$match FROM modules_$sys_lang m WHERE mod_modid = $mod_modid AND module_active = '".MOD_ACTIVE."' ORDER BY module_pos";
		//print "$sql\n";
		$ret = array();
		$data = $db->Execute($sql);
		foreach($data as $item) {
			$ret[$item['module_id']] = $this->load_tree($item['mod_id'], $q, ($registrated ? $registrated : $item['module_type']));
			$ret[$item['module_id']]['_data_'] = $item;
			$ret[$item['module_id']]['_data_']['registrated'] = $registrated;
		}

		return $ret;
	} // load_tree

	/*
	function unset_bad(&$data)
	{
		$ret = 1;
		if(isset($data['_data_']))
		foreach($data as $key=>$item) {
			if($key == '_data_' && $item['score'] != 0) {
				$ret = 0;
				continue;
			}

			if($key[0] == '_') {
				array_splice($data, $key, 1);
				continue;
			}

			if($this->unset_bad($data[$key])) {
				array_splice($data, $key, 1);
			} else
				$ret = 0;
		}

		return $ret;
	}
*/
	function search($q)
	{
		$data = $this->load_tree(0, $q);
		//$this->unset_bad($data);

		return $data;
	}

	/* meklee peec id */
	function find($mod_id)
	{

		$mod_id = (integer)$mod_id;

		if($mod_id) {

			if(!is_array($this->data))
				$this->data = $this->load();

			foreach($this->data as $item)
				if($item['mod_id'] == $mod_id)
					return $item;
		} else
			return false;

		return 0;
	} // find

	function get_path($mod_id, $mod_modid = 0, $path = '')
	{
		global $db, $sys_lang;

		$sql = "SELECT module_id, mod_id FROM modules_$sys_lang WHERE mod_modid = $mod_modid";
		$data = $db->Execute($sql);

		if(!count($data))
			return '';

		foreach($data as $item) {
			if($item['mod_id'] == $mod_id)
				return $path."/$item[module_id]/";
			$p = $this->get_path($mod_id, $item['mod_id'], "$path/$item[module_id]");
			if($p)
				return $p;
		}
	} // get_path

	function set_module(&$template, &$module, $block = 'BLOCK_modules',
		$module_path = '', $padding = 0, $padding_char = '&nbsp;')
	{
		global $module_count;

		if(!isset($module_count))
			$module_count = 0;

		++$module_count;

		foreach($module as $key => $val)
			$template->set_var($key, $val);
		if($module['module_name'] == '')
			$template->set_var('module_name', '-nezināms-');

		$template->set_var('module_color_class', 'box-normal');
		if($module['module_active'] == MOD_ACTIVE)
			$template->set_var('module_active_y', ' selected');
		else {
			$template->set_var('module_active_n', ' selected');
			$template->set_var('module_color_class', 'box-inactive');
		}

		if($module['module_visible'] == MOD_VISIBLE) {
			$template->set_var('module_visible_y', ' selected');
		} else {
			$template->set_var('module_visible_n', ' selected');
			$template->set_var('module_color_class', 'box-invisible');
		}

		/* ja neaktiivs un neredzams */
		if($module['module_active'] == MOD_INACTIVE && $module['module_visible'] == MOD_INVISIBLE)
			$template->set_var('module_color_class', 'box-inactive-invisible');

		if($padding)
			$template->set_var('module_class', 'box-cat-small');
		else
			$template->set_var('module_class', 'box-cat');

		// type
		if($module['module_type'] == MOD_TYPE_OPEN)
			$template->set_var('module_type_o', ' selected');
		elseif($module['module_type'] == MOD_TYPE_REGISTRATED)
			$template->set_var('module_type_r', ' selected');

		$template->set_var('module_path', $module_path);
		$template->set_var('module_padding', str_repeat($padding_char, $padding * 3));

		$template->set_var('item_count', $module_count);
		$template->parse_block($block, TMPL_APPEND);
	} // set_module

	function set_modules(&$template, $mod_id = 0, $block = 'BLOCK_modules',
		$mod_modid = 0, $d = 0, $module_path = '')
	{
		global $db, $sys_lang;

		$sql = "SELECT * FROM modules_$sys_lang WHERE mod_modid = $mod_modid AND module_active = '".MOD_ACTIVE."' AND module_visible = '".MOD_VISIBLE."' ORDER BY module_pos";

		$data = $db->Execute($sql);

		if($d > 5)
			return;

		foreach($data as $module) {
			if(($mod_id && $module['mod_id'] == $mod_id) || !$mod_id) {
				$new_module_path = $module_path.$module['module_id'].'/';
				$this->set_module($template, $module, $block, $new_module_path, $d);
				$this->set_modules($template, $mod_id, $block, $module['mod_id'], $d + 1, $new_module_path);
			}
		}
	} // set_modules

	function set_modules_all(&$template, $mod_id = 0, $block = 'BLOCK_modules',
		$mod_modid = 0, $d = 0, $module_path = '')
	{
		global $db, $sys_lang;

		$sql = "SELECT * FROM modules_$sys_lang WHERE mod_modid = $mod_modid ORDER BY module_pos";

		$data = $db->Execute($sql);

		if($d > 5)
			return;

		foreach($data as $module) {
			if(($mod_id && $module['mod_id'] == $mod_id) || !$mod_id) {
				$new_module_path = $module_path.$module['module_id'].'/';
				$this->set_module($template, $module, $block, $new_module_path, $d);
				$this->set_modules_all($template, $mod_id, $block, $module['mod_id'], $d + 1, $new_module_path);
			}
		}
	} // set_modules_all

} // Module
