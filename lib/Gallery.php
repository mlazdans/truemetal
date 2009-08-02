<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

define('GALLERY_ACTIVE', 'Y');
define('GALLERY_INACTIVE', 'N');
define('GALLERY_VISIBLE', 'Y');
define('GALLERY_INVISIBLE', 'N');
define('GALLERY_ALL', false);
define('GALLERY_VALIDATE', true);
define('GALLERY_DONTVALIDATE', false);

define('GALLERY_DATA_VISIBLE', 'Y');
define('GALLERY_DATA_INVISIBLE', 'N');
define('GALLERY_DATA_ALL', false);

class Gallery
{
	var $error_msg;

	function __construct()
	{
	} // __construct

	function load_data($gd_id, $gd_galid = 0, $gd_visible = GALLERY_DATA_VISIBLE,
		$gal_active = GALLERY_ACTIVE)
	{
		global $db, $ip;

		$gd_id = (integer)$gd_id;
		$gd_galid = (integer)$gd_galid;

		$sql = "";
		$sql_add = 'gd.gd_galid = g.gal_id AND ';
		if(!$gd_id)
			$sql .= "SET @gd_pos = 0;";

		$sql = "SELECT gd.*";

		if($gd_id)
			$sql .= ", (SELECT COUNT(*) FROM truemetal_gallery.gallery_data WHERE gd_galid = g.gal_id AND gd_id <= gd.gd_id) gd_pos";
		else
			$sql .= ", (@gd_pos := @gd_pos + 1) AS gd_pos";

		$sql .= " FROM truemetal_gallery.gallery_data gd, gallery g";

		if($gd_id)
			$sql_add .= "gd.gd_id = $gd_id AND ";

		if($gd_galid)
			$sql_add .= "gd.gd_galid = $gd_galid AND ";

		if($gal_active)
			$sql_add .= "g.gal_active = '$gal_active' AND ";

		if($gd_visible)
			$sql_add .= "gd.gd_visible = '$gd_visible' AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' ORDER BY gd_filename';

		if($gd_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load_data

	function get_next_data($gal_id, $gd_id)
	{
		global $db;

		$gal_id = (integer)$gal_id;
		$gd_id = (integer)$gd_id;

		$sql = "SELECT gd_id FROM truemetal_gallery.gallery_data WHERE gd_visible = 'Y' AND gd_galid = $gal_id AND gd_id > $gd_id LIMIT 0,1";
		$data = $db->ExecuteSingle($sql);

		return isset($data['gd_id']) ? $data['gd_id'] : 0;
	} // get_next_data

	function load($gal_id = 0, $gal_active = GALLERY_ACTIVE,
		$gal_visible = GALLERY_ALL)
	{
		global $db, $ip;

		$gal_id = (integer)$gal_id;

		$sql_add = '';
		$sql = "
SELECT
	*,
	(SELECT COUNT(*) FROM truemetal_gallery.gallery_data WHERE gd_galid = gallery.gal_id) gd_count
FROM gallery
";
		if($gal_id)
			$sql_add .= "gal_id = $gal_id AND ";

		if($gal_active)
			$sql_add .= "gal_active = '$gal_active' AND ";

		if($gal_visible)
			$sql_add .= "gal_visible = '$gal_visible' AND ";

		$sql_add = substr($sql_add, 0, -4);

		$sql .= ' LEFT JOIN gallery_group ON gg_id = gal_ggid';
		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		$sql .= ' ORDER BY gal_entered DESC, gg_date DESC';

		if($gal_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function insert(&$data, $validate = GALLERY_VALIDATE)
	{
		global $db;

		if($validate)
			$this->validate($data);

		$date = $db->now();
		if($data['gal_entered'])
			$date = "'$data[gal_entered]'";

		$sql = "
		INSERT INTO gallery (
			gal_name, gal_active, gal_visible,
			gal_data, gal_entered, gal_ggid
		) VALUES (
			'$data[gal_name]', '$data[gal_active]', '$data[gal_visible]',
			'$data[gal_data]',  $date, $data[gal_ggid]
		)";

		return ($db->Execute($sql) ? $db->LastID() : false);
	} // insert

	function update($gal_id, &$data, $validate = GALLERY_VALIDATE)
	{
		global $db;

		$gal_id = (integer)$gal_id;

		if(!$gal_id) {
			$this->error_msg = 'Nav norādīts vai nepareizs galerijas ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE gallery SET ';
		$sql .= $data['gal_name'] ? "gal_name = '$data[gal_name]', " : '';
		$sql .= $data['gal_entered'] ? "gal_entered = '$data[gal_entered]', " : '';
		$sql .= $data['gal_data'] ? "gal_data = '$data[gal_data]', " : '';
		$sql .= "gal_active = '$data[gal_active]', ";
		$sql .= "gal_visible = '$data[gal_visible]', ";
		$sql .= "gal_data = '$data[gal_data]', ";
		$sql .= "gal_ggid = $data[gal_ggid], ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE gal_id = '.$gal_id;
		$db->Execute($sql);
		if(isset($data['gd_descr']) && is_array($data['gd_descr'])) {
			foreach($data['gd_descr'] as $key=>$descr) {
				$sql = "UPDATE truemetal_gallery.gallery_data SET gd_descr='$descr' WHERE gd_id = $key";
				$db->Execute($sql);
			}
		}

		return $gal_id;
	} // update

	function save($gal_id, &$data)
	{
		$this->validate($data);

		$gal_id = (integer)$gal_id;

		$error_msg = '';

		if(!$data['gal_name'])
			$error_msg .= 'Nav norādīts galerijas nosaukums<br>';

		if(!$error_msg) {
			if($gal_id)
				return $this->update($gal_id, $data, GALLERY_DONTVALIDATE);
			else
				return $this->insert($data, GALLERY_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	} // save

	function del($gal_id)
	{
		global $db;

		$gal_id = (integer)$gal_id;

		if(!$gal_id)
			return true;

		$sql = 'DELETE FROM gallery WHERE gal_id = '.$gal_id;

		return $db->Execute($sql);
	} // del

	function activate($gal_id)
	{
		global $db;

		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_active = "'.GALLERY_ACTIVE.'" WHERE gal_id = '.$gal_id;

		return $db->Execute($sql);
	} // activate

	function deactivate($gal_id)
	{
		global $db;

		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_active = "'.GALLERY_INACTIVE.'" WHERE gal_id = '.$gal_id;

		return $db->Execute($sql);
	} // deactivate

	function show($gal_id)
	{
		global $db;

		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_visible = "'.GALLERY_VISIBLE.'" WHERE gal_id = '.$gal_id;

		return $db->Execute($sql);
	} // show

	function hide($gal_id)
	{
		global $db;

		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_visible = "'.GALLERY_INVSIBLE.'" WHERE gal_id = '.$gal_id;

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

		if(isset($data['gallery_count']) && $func)
			for($r = 1; $r <= $data['gallery_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['gal_checked'.$r]) && isset($data['gal_id'.$r]))
					$ret = $ret && $this->{$func}($data['gal_id'.$r]);

		return $ret;
	} // process_action

	function validate(&$data)
	{
		if(isset($data['gal_active']))
			$data['gal_active'] = ereg('[^YN]', $data['gal_active']) ? '' : $data['gal_active'];
		else
			$data['gal_active'] = GALLERY_ACTIVE;

		if(isset($data['gal_visible']))
			$data['gal_visible'] = ereg('[^YN]', $data['gal_visible']) ? '' : $data['gal_visible'];
		else
			$data['gal_visible'] = GALLERY_VISIBLE;

		if(!isset($data['gal_name']))
			$data['gal_name'] = '';

		$data['gal_data'] = &$data['editor_data'];

		if(!isset($data['gal_entered']))
			$data['gal_entered'] = '';

		if(isset($data['gal_ggid']))
			$data['gal_ggid'] = ereg('[^0-9]', $data['gal_ggid']) ? 0 : $data['gal_ggid'];
		else
			$data['gal_ggid'] = 0;
	} // validate

} // Gallery

