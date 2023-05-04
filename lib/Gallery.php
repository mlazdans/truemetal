<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Res.php');
require_once('lib/Table.php');

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

	function load($params = array())
	{
		if(!is_array($params))
			$params = array('gal_id'=>$params);

		$sql_add = array();

		if(isset($params['gal_id']))
			$sql_add[] = sprintf("gal_id = %d", $params['gal_id']);

		if(isset($params['res_id']))
			$sql_add[] = sprintf("res_id = %d", $params['res_id']);

		if(isset($params['gal_visible']))
		{
			if($params['gal_visible'])
				$sql_add[] = sprintf("gal_visible = '%s'", $params['gal_visible']);
		} else {
			$sql_add[] = sprintf("gal_visible = '%s'", Res::STATE_VISIBLE);
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

	function insert(&$data, $validate = Res::ACT_VALIDATE)
	{
		if($validate)
			$this->validate($data);

		$date = $this->db->now();
		if($data['gal_entered'])
			$date = "'$data[gal_entered]'";

		$sql = "
		INSERT INTO gallery (
			gal_name, gal_visible,
			gal_data, gal_entered, gal_ggid
		) VALUES (
			'$data[gal_name]', '$data[gal_visible]',
			'$data[gal_data]',  $date, $data[gal_ggid]
		)";

		if($this->db->Execute($sql))
			return last_insert_id();
		else
			return false;
	} // insert

	function update($gal_id, &$data, $validate = Res::ACT_VALIDATE)
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
				return $this->update($gal_id, $data, Res::ACT_DONTVALIDATE);
			else
				return $this->insert($data, Res::ACT_DONTVALIDATE);
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

	function show($gal_id)
	{
		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_visible = "'.Res::STATE_VISIBLE.'" WHERE gal_id = '.$gal_id;

		return $this->db->Execute($sql);
	} // show

	function hide($gal_id)
	{
		$gal_id = (integer)$gal_id;

		$sql = 'UPDATE gallery SET gal_visible = "'.Res::STATE_INVISIBLE.'" WHERE gal_id = '.$gal_id;

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

	# TODO: validāciju atstāt izsaucēja ziņā
	function validate(&$data)
	{
		if(isset($data['gal_visible']))
			$data['gal_visible'] = ereg('[^YN]', $data['gal_visible']) ? '' : $data['gal_visible'];
		else
			$data['gal_visible'] = Res::STATE_VISIBLE;

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

	public static function Route($resource, $c_id = 0)
	{
		return "/gallery/$resource[gal_id]/".($c_id ? "#comment$c_id" : "");
	} // Route
} // Gallery

