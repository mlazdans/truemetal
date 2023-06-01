<?php declare(strict_types = 1);

use dqdp\SQL\Select;

// define('MOD_ALL', -1);
// define('MOD_NONE', -2);
// define('MOD_ACTIVE', 'Y');
// define('MOD_INACTIVE', 'N');
// define('MOD_VISIBLE', 'Y');
// define('MOD_INVISIBLE', 'N');
// define('MOD_TYPE_OPEN', 'O');
// define('MOD_TYPE_REGISTRATED', 'R');

class Module
{
	var $data = array();
	var $error_msg;

	static function load(array $params)
	{
		$sql = (new Select)->From("modules");

		if(isset($params['mod_id'])){
			$sql->Where(["mod_id = ?", $params['mod_id']]);
		}

		if(isset($params['module_id'])){
			$sql->Where(["module_id = ?", $params['module_id']]);
		}

		if(falsed($params, 'modules.mod_modid'))
		{
			$sql->Where("modules.mod_modid = NULL");
		} elseif(!empty($params['mod_modid'])){
			$sql->Where(["modules.mod_modid = ?", $params['mod_modid']]);
		}

		if(defaulted($params, 'module_active'))
		{
			$sql->Where("modules.module_active = 1");
		} elseif(!ignored($params, 'module_active')){
			$sql->Where(["modules.module_active = ?", $params['module_active']]);
		}

		if(defaulted($params, 'module_visible'))
		{
			$sql->Where("modules.module_visible = 1");
		} elseif(!ignored($params, 'module_visible')){
			$sql->Where(["modules.module_visible = ?", $params['module_visible']]);
		}

		$sql->OrderBy("modules.mod_modid, modules.module_pos");

		if(
			isset($params['mod_id']) ||
			(isset($params['module_id']) && isset($params['mod_modid']))
			)
		{
			return DB::ExecuteSingle($sql);
		} else {
			return DB::Execute($sql);
		}
	}

	static function get_tree(?int $mod_modid, array $params = []): array  {
		if($mod_modid){
			$params = ['mod_modid'=>$mod_modid];
		} else {
			$params = ['mod_modid'=>false];
		}

		$data = static::load($params);

		foreach($data as $item){
			$item['module_tree'] = static::get_tree($item['mod_id'], $params);
			$ret[$item['module_id']] = $item;
		}

		return $ret??[];
	}

	// function get_item($key)
	// {
	// 	if(!isset($this->data) || !count($this->data))
	// 		$this->load($key);

	// 	return isset($this->data[$key]) ? $this->data[$key] : false;
	// } // get_item

	function insert(&$data)
	{
		$sql = "INSERT INTO `modules` (".
			"module_id, mod_modid, module_name, module_descr,".
			"module_active, module_pos, module_data, module_entered,".
			"module_visible, module_type".
		") VALUES (".
			"'$data[module_id]', $data[mod_modid], '$data[module_name]', '$data[module_descr]',".
			"'$data[module_active]', $data[module_pos], '$data[module_data]', NOW(),".
			"'$data[module_visible]', '$data[module_type]'".
		")";

		$ret = DB::Execute($sql);

		if($ret) {
			$last_id = DB::LastID();
			$sql = "UPDATE `modules` SET ".
				"module_pos = module_pos + 1 ".
				"WHERE ".
				"module_pos >= $data[module_pos] AND ".
				"mod_id != $last_id AND ".
				"mod_modid = $data[mod_modid]";
			DB::execute($sql);
			return $last_id;
		}

		return false;
	} // insert

	function update(&$data)
	{
		$data2 = $this->get_item($data['mod_id']);

		$sql = "UPDATE `modules` SET ".
			"module_id = '$data[module_id]', module_name = '$data[module_name]', module_descr = '$data[module_descr]',".
			"module_active = '$data[module_active]', module_pos = $data[module_pos],".
			"module_data = '$data[module_data]',".
			"module_visible = '$data[module_visible]',".
			"module_type = '$data[module_type]'".
		"WHERE ".
			"mod_id = $data[mod_id]";

		$ret = DB::execute($sql);

		if($ret) {
			$sql = '';
			if($data['module_pos'] > $data2['module_pos'])
				$sql = "UPDATE `modules` SET ".
					"module_pos = module_pos - 1 ".
					"WHERE ".
					"module_pos >= $data2[module_pos] AND ".
					"module_pos <= $data[module_pos] AND ".
					"mod_id != $data[mod_id] AND ".
					"mod_modid = $data2[mod_modid]";
			elseif($data['module_pos'] < $data2['module_pos'])
				$sql = "UPDATE `modules` SET ".
					"module_pos = module_pos + 1 ".
					"WHERE ".
					"module_pos <= $data2[module_pos] AND ".
					"module_pos >= $data[module_pos] AND ".
					"mod_id != $data[mod_id] AND ".
					"mod_modid = $data2[mod_modid]";
			if($sql)
				DB::execute($sql);
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

	function activate($mod_id)
	{
		$mod_id = (integer)$mod_id;
		$sql = "UPDATE `modules` SET module_active = '".MOD_ACTIVE."' WHERE mod_id = $mod_id";

		return DB::Execute($sql);
	} // activate

	function deactivate($mod_id)
	{
		$mod_id = (integer)$mod_id;
		$sql = "UPDATE `modules` SET module_active = '".MOD_INACTIVE."' WHERE mod_id = $mod_id";

		return DB::Execute($sql);
	} // deactivate

	function show($mod_id)
	{
		$mod_id = (integer)$mod_id;
		$sql = "UPDATE `modules` SET module_visible = '".MOD_VISIBLE."' WHERE mod_id = $mod_id";

		return DB::Execute($sql);
	} // show

	function hide($mod_id)
	{
		$mod_id = (integer)$mod_id;
		$sql = "UPDATE `modules` SET module_visible = '".MOD_INVISIBLE."' WHERE mod_id = $mod_id";

		return DB::Execute($sql);
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

		if(isset($data['module_count']) && $func)
			for($r = 1; $r <= $data['module_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['mod_checked'.$r]) && isset($data['mod_id'.$r]))
					$ret = $ret && $this->{$func}($data['mod_id'.$r]);

		return $ret;
	} // process_action

	private function load_tree2(int $mod_modid = 0, $q = '', $registrated = false)
	{
		$sql_add = array(
			"module_active = '".MOD_ACTIVE."'",
			);

		if(!$mod_modid){
			$sql_add[] = "mod_modid IS NULL";
		} else {
			$sql_add[] = "mod_modid = $mod_modid";
		}

		$match = '';
		if($q)
			$match = ', '.search_to_sql_legacy($q, array('module_name', 'module_data')).' score';


		$sql = "SELECT m.*$match FROM `modules` m";
		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);
		$sql .= " ORDER BY module_pos";

		$ret = array();
		$data = DB::Execute($sql);
		foreach($data as $item) {
			$ret[$item['module_id']] = $this->load_tree($item['mod_id'], $q, ($registrated ? $registrated : $item['module_type']));
			$ret[$item['module_id']]['_data_'] = $item;
			$ret[$item['module_id']]['_data_']['registrated'] = $registrated;
		}

		return $ret;
	} // load_tree

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
		$sql_add = array();
		if(!$mod_modid){
			$sql_add[] = "mod_modid IS NULL";
		} else {
			$sql_add[] = "mod_modid = $mod_modid";
		}

		$sql = "SELECT module_id, mod_id FROM `modules`";
		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$data = DB::Execute($sql);

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
			$template->set_var('module_active_y', ' selected="selected"');
		else {
			$template->set_var('module_active_n', ' selected="selected"');
			$template->set_var('module_color_class', 'box-inactive');
		}

		if($module['module_visible'] == MOD_VISIBLE) {
			$template->set_var('module_visible_y', ' selected="selected"');
		} else {
			$template->set_var('module_visible_n', ' selected="selected"');
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
			$template->set_var('module_type_o', ' selected="selected"');
		elseif($module['module_type'] == MOD_TYPE_REGISTRATED)
			$template->set_var('module_type_r', ' selected="selected"');

		$template->set_var('module_path', $module_path);
		$template->set_var('module_padding', str_repeat($padding_char, $padding * 3));

		$template->set_var('module_count', $module_count);
		$template->parse_block($block, TMPL_APPEND);
	} // set_module

	function set_modules(&$template, $mod_id = 0, $block = 'BLOCK_modules',
		$mod_modid = 0, $d = 0, $module_path = '')
	{
		$sql_add = array(
			"module_active = '".MOD_ACTIVE."'",
			"module_visible = '".MOD_VISIBLE."'",
			);
		if(!$mod_modid){
			$sql_add[] = "mod_modid IS NULL";
		} else {
			$sql_add[] = "mod_modid = $mod_modid";
		}

		$sql = "SELECT * FROM `modules`";
		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= " ORDER BY module_pos";

		$data = DB::Execute($sql);

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
		$sql_add = array();
		if(!$mod_modid){
			$sql_add[] = "mod_modid IS NULL";
		} else {
			$sql_add[] = "mod_modid = $mod_modid";
		}

		$sql = "SELECT * FROM `modules`";
		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);
		$sql .= " ORDER BY module_pos";

		$data = DB::Execute($sql);

		if($d > 5)
			return;

		foreach($data as $module)
		{
			if($mod_id && ($module['mod_id'] == $mod_id))
				$template->set_var("module_selected", ' selected="selected"', $block);
			else
				$template->set_var("module_selected", '', $block);
			//if(($mod_id && $module['mod_id'] == $mod_id) || !$mod_id) {
				$new_module_path = $module_path.$module['module_id'].'/';
				$this->set_module($template, $module, $block, $new_module_path, $d);
				$this->set_modules_all($template, $mod_id, $block, $module['mod_id'], $d + 1, $new_module_path);
			//}
		}
	} // set_modules_all

} // Module

