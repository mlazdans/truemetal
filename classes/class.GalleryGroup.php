<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

define('GALLERY_GROUP_VALIDATE', true);
define('GALLERY_GROUP_DONTVALIDATE', false);

class GalleryGroup
{
	var $error_msg;

	function __construct()
	{
	} // __construct

	function load($gg_id = 0)
	{
		global $db;

		$gg_id = (integer)$gg_id;

		$sql_add = '';
		$sql = 'SELECT * FROM gallery_group';
		if($gg_id)
			 $sql_add .= "gg_id = $gg_id AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' ORDER BY gg_date DESC';

		if($gg_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function insert(&$data, $validate = GALLERY_GROUP_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$date = $db->now();
		if($data['gg_entered'])
			$date = "'$data[gg_entered]'";

		$sql = "
		INSERT INTO gallery_group (
			gg_name, gg_data, gg_date, gg_entered
		) VALUES (
			'$data[gg_name]', '$data[gg_data]', '$data[gg_date]', $date
		)";

		return ($db->Execute($sql) ? $db->LastID() : false);
	} // insert

	function update($gg_id, &$data, $validate = GALLERY_GROUP_VALIDATE)
	{
		global $db;

		$gg_id = (integer)$gg_id;

		if(!$gg_id) {
			$this->error_msg = 'Nav norādīts vai nepareizs galerijas grupas ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE gallery_group SET ';
		$sql .= $data['gg_entered'] ? "gg_entered = '$data[gg_entered]', " : '';
		$sql .= "gg_date = '$data[gg_date]', ";
		$sql .= "gg_name = '$data[gg_name]', ";
		$sql .= "gg_data = '$data[gg_data]', ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE gg_id = '.$gg_id;

		$db->Execute($sql);
		return $gg_id;
	} // update

	function save($gg_id, &$data)
	{
		$this->validate($data);

		$gg_id = (integer)$gg_id;

		$error_msg = '';

		if(!$data['gg_name'])
			$error_msg .= 'Nav norādīts galerijas grupas nosaukums<br>';

		if(!$data['gg_date'])
			$error_msg .= 'Nav norādīts galerijas grupas datums<br>';

		if(!$error_msg) {
			if($gg_id)
				return $this->update($gg_id, $data, GALLERY_GROUP_DONTVALIDATE);
			else
				return $this->insert($data, GALLERY_GROUP_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save

	function del($gg_id)
	{
		global $db;

		$gg_id = (integer)$gg_id;

		if(!$gg_id)
			return true;

		$sql = 'DELETE FROM gallery_group WHERE gg_id = '.$gg_id;

		return $db->Execute($sql);
	} // del

	function process_action(&$data, $action)
	{
		$ret = true;
		$func = '';

		if($action == 'delete_multiple')
			$func = 'del';

		if(isset($data['gallery_group_count']) && $func)
			for($r = 1; $r <= $data['gallery_group_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['gg_checked'.$r]) && isset($data['gg_id'.$r]))
					$ret = $ret && $this->{$func}($data['gg_id'.$r]);

		return $ret;
	} // process_action

	function validate(&$data)
	{
		if(!isset($data['gg_name']))
			$data['gg_name'] = '';

		if(!isset($data['gal_entered']))
			$data['gal_entered'] = '';

		$data['gg_data'] = &$data['editor_data'];

		if(!isset($data['gg_date']))
			$data['gg_date'] = '';

	} // validate

} // GalleryGroup
