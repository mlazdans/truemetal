<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Comment.php');
require_once('lib/Res.php');

class ResComment extends Res
{
	function __construct() {
		parent::__construct();
	} // __construct

	function Connect($res_id, $c_id)
	{
		$sql = sprintf(
			"INSERT INTO res_comment (res_id, c_id) VALUES ('%d', '%d')",
			$res_id,
			$c_id
			);

		return $this->db->Execute($sql);
	} // Connect

	//function Add($res_id, $data)
	function Add()
	{
		list($res_id, $data) = func_get_args();

		$this->InitDb();

		$Comment = new Comment();
		$Comment->setDb($this->db);
		if(!$Comment->Add($data)) {
			return false;
		}

		$c_id = $this->db->LastID();

		return ($this->Connect($res_id, $c_id) ? $c_id : false);
	} // Add

	function Get(Array $params = array())
	{
		$this->InitDb();

		# NOTE: Man liekas, ka comment_map nah nav vajadzīgs, 2014-03-01
/*
		$sql = "
SELECT
	c.*,
	r.res_votes,
	cm.cm_old_id,
	r2.res_id AS parent_res_id,
	r2.table_id AS parent_table_id
FROM
	res_comment rc
JOIN comment c ON c.c_id = rc.c_id
JOIN res r ON r.res_id = c.res_id
JOIN res r2 ON r2.res_id = rc.res_id
LEFT JOIN comment_map cm ON cm.cm_new_id = c.c_id
";
*/
		$sql = "
SELECT
	c.*,
	0 as cm_old_id,
	r.res_votes,
	r2.res_id AS parent_res_id,
	r2.table_id AS parent_table_id
FROM
	res_comment rc
JOIN comment c ON c.c_id = rc.c_id
JOIN res r ON r.res_id = c.res_id
JOIN res r2 ON r2.res_id = rc.res_id
";

		$sql_add = array();

		if(isset($params['c_visible']))
		{
			if($params['c_visible'])
				$sql_add[] = sprintf("c.c_visible = '%s'", $params['c_visible']);
		} else {
			$sql_add[] = sprintf("c.c_visible = '%s'", Res::STATE_VISIBLE);
		}

		# IPS
		if(isset($params['ips']))
		{
			if(is_array($params['ips']))
			{
				$sql_add[] = sprintf("c_userip IN (%s)", "'".join("','", $params['ips'])."'");
			} else {
				$sql_add[] = sprintf("c_userip = '%s'", $params['ips']);
			}
		}

		if(!empty($params['login_id']))
			$sql_add[] = sprintf("(c.login_id = %d)", $params['login_id']);

		if(!empty($params['res_id']))
			$sql_add[] = sprintf("(rc.res_id = %d)", $params['res_id']);

		if(!empty($params['cres_id']))
			$sql_add[] = sprintf("(c.res_id = %d)", $params['cres_id']);

		if(!empty($params['c_id']))
			$sql_add[] = sprintf("(c.c_id = %d)", $params['c_id']);

		if($sql_add)
			$sql .= " WHERE ".join(' AND ', $sql_add);

		if(empty($params['order']))
			$sql .= " ORDER BY c_entered ";
		else
			$sql .= " ORDER BY $params[order] ";

		if(!empty($params['limit']))
			$sql .= " LIMIT ".$params['limit'];

		if(!empty($params['c_id']))
		{
			return $this->db->ExecuteSingle($sql);
		} else {
			return $this->db->Execute($sql);
		}
	} // Get

} // class::ResComment

