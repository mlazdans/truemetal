<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Res.php');
require_once('lib/Table.php');
require_once('lib/Gallery.php');

class GalleryData extends Res
{
	protected $table_id = Table::GALLERY_DATA;

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
			$params = array('gd_id'=>$params);

		$sql_add = array();

		$sql = "
		SELECT
			gd_id, gd.res_id, gd.gal_id, gd_descr, gd_entered, r.res_votes,
			COALESCE(r.res_comment_count, 0) AS res_comment_count,
			res_comment_lastdate
			";

		if(!empty($params['load_images']))
			$sql .= ', gd_data, gd_thumb';

		$sql .= ' FROM gallery_data gd';
		$sql .= ' JOIN gallery g ON g.gal_id = gd.gal_id';
		$sql .= ' JOIN res r ON r.res_id = gd.res_id';

		if(isset($params['gd_id']))
			$sql_add[] = sprintf("gd_id = %d", $params['gd_id']);

		if(isset($params['gal_id']))
			$sql_add[] = sprintf("gd.gal_id = %d", $params['gal_id']);

		if(isset($params['res_id']))
			$sql_add[] = sprintf("gd.res_id = %d", $params['res_id']);

		if(isset($params['gal_active']))
		{
			if($params['gal_active'])
				$sql_add[] = sprintf("gal_active = '%s'", $params['gal_active']);
		} else {
			$sql_add[] = sprintf("gal_active = '%s'", Res::STATE_ACTIVE);
		}

		if(isset($params['gd_visible']))
		{
			if($params['gd_visible'])
				$sql_add[] = sprintf("gd_visible = '%s'", $params['gd_visible']);
		} else {
			$sql_add[] = sprintf("gd_visible = '%s'", Res::STATE_VISIBLE);
		}

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= (empty($params['order']) ? " ORDER BY gd_filename " : " ORDER BY $params[order] ");

		if(isset($params['limit']))
			$sql .= " LIMIT $params[limit]";

		return (isset($params['gd_id']) || isset($params['res_id']) ? $this->db->ExecuteSingle($sql) : $this->db->Execute($sql));
	} // load

	function get_next_data($gal_id, $gd_id)
	{
		$gal_id = (integer)$gal_id;
		$gd_id = (integer)$gd_id;

		$sql = "SELECT gd_id FROM gallery_data WHERE gd_visible = '".Res::STATE_VISIBLE."' AND gal_id = $gal_id AND gd_id > $gd_id LIMIT 0,1";
		$data = $this->db->ExecuteSingle($sql);

		return isset($data['gd_id']) ? $data['gd_id'] : 0;
	} // get_next_data

	function del($gd_id)
	{
		$sql = sprintf('DELETE FROM gallery_data WHERE gd_id = %d', $gd_id);

		return $this->db->Execute($sql);
	} // del

} // GalleryData

