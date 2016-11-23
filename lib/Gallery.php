<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Res.php');
require_once('lib/Table.php');

define('GALLERY_ACTIVE', 'Y');
define('GALLERY_INACTIVE', 'N');
define('GALLERY_VISIBLE', 'Y');
define('GALLERY_INVISIBLE', 'N');
define('GALLERY_ALL', false);
define('GALLERY_VALIDATE', true);
define('GALLERY_DONTVALIDATE', false);

class Gallery extends Res
{
	protected $table_id = Table::GALLERY;

	var $error_msg;

	function __construct()
	{
		global $db;

		parent::__construct();

		$this->SetDb($db);
	} // __construct

	/*
	function load($gal_id = 0, $gal_active = GALLERY_ACTIVE,
		$gal_visible = GALLERY_ALL)
	{*/
	function load($params = array())
	{
		if(!is_array($params))
			$params = array('gal_id'=>$params);

		$sql_add = array();

		if(isset($params['gal_id']))
			$sql_add[] = sprintf("gal_id = %d", $params['gal_id']);

		if(isset($params['res_id']))
			$sql_add[] = sprintf("res_id = %d", $params['res_id']);

		if(isset($params['gal_active']))
		{
			if($params['gal_active'])
				$sql_add[] = sprintf("gal_active = '%s'", $params['gal_active']);
		} else {
			$sql_add[] = sprintf("gal_active = '%s'", GALLERY_ACTIVE);
		}

		$sql = 'SELECT * FROM gallery g';
		$sql .= ' JOIN res r ON r.res_id = g.res_id';
		$sql .= ' LEFT JOIN gallery_group_old ON gg_id = gal_ggid';

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= (empty($params['order']) ? " ORDER BY gal_entered DESC, gg_date DESC " : " ORDER BY $params[order] ");

		if(isset($params['limit']))
			$sql .= " LIMIT $params[limit]";

		return (isset($params['gal_id']) || isset($params['res_id']) ? $this->db->ExecuteSingle($sql) : $this->db->Execute($sql));
	} // load

	function insert(&$data, $validate = GALLERY_VALIDATE)
	{
		if($validate)
			$this->validate($data);

		$date = $this->db->now();
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

		if($this->db->Execute($sql))
			return last_insert_id();
		else
			return false;
	} // insert

	function update($gal_id, &$data, $validate = GALLERY_VALIDATE)
	{
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
		$this->db->Execute($sql);
		if(isset($data['gd_descr']) && is_array($data['gd_descr'])) {
			foreach($data['gd_descr'] as $key=>$descr) {
				$sql = "UPDATE gallery_data SET gd_descr='$descr' WHERE gd_id = $key";
				$this->db->Execute($sql);
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
		$gal_id = (integer)$gal_id;

		if(!$gal_id)
			return true;

		$sql = 'DELETE FROM gallery WHERE gal_id = '.$gal_id;

		return $this->db->Execute($sql);
	} // del

	function activate($gal_id)
	{
		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_active = "'.GALLERY_ACTIVE.'" WHERE gal_id = '.$gal_id;

		return $this->db->Execute($sql);
	} // activate

	function deactivate($gal_id)
	{
		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_active = "'.GALLERY_INACTIVE.'" WHERE gal_id = '.$gal_id;

		return $this->db->Execute($sql);
	} // deactivate

	function show($gal_id)
	{
		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_visible = "'.GALLERY_VISIBLE.'" WHERE gal_id = '.$gal_id;

		return $this->db->Execute($sql);
	} // show

	function hide($gal_id)
	{
		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_visible = "'.GALLERY_INVSIBLE.'" WHERE gal_id = '.$gal_id;

		return $this->db->Execute($sql);
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

