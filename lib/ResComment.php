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
			"INSERT INTO `res_comment` (`res_id`, `c_id`) VALUES ('%d', '%d')",
			$res_id,
			$c_id
			);

		return $this->db->Execute($sql);
	} // Connect

	function Add($res_id, $data)
	{
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

		$sql = "
SELECT
	c.*,
	r.res_votes AS c_votes,
	cm.cm_old_id
FROM
	res_comment rc
JOIN comment c ON c.c_id = rc.c_id
JOIN res r ON r.res_id = c.res_id
LEFT JOIN comment_map cm ON cm.cm_new_id = c.c_id
";

		$sql_add = array();

		/*
		if($this->table !== false)
			$sql_add[] = "(cc_table = '$this->table')";

		if(!empty($params['tables_exclude']))
			$sql_add[] = "cc_table NOT IN ('".join("','", $params['tables_exclude'])."')";

		if(!empty($params['cc_table_id']))
			$sql_add[] = sprintf("(cc_table_id = %d)", $params['cc_table_id']);

		if(isset($params['c_visible']))
		{
			if($params['c_visible'])
				$sql_add[] = sprintf("c_visible = '%s'", $params['c_visible']);
		} else {
			$sql_add[] = sprintf("c_visible = '%s'", COMMENT_VISIBLE);
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
			$sql_add[] = sprintf("(login_id = %d)", $params['login_id']);
		*/

		if(!empty($params['res_id']))
			$sql_add[] = sprintf("(rc.res_id = %d)", $params['res_id']);

		if($sql_add)
			$sql .= " WHERE ".join(' AND ', $sql_add);

		if(empty($params['order']))
			$sql .= " ORDER BY c_entered ";
		else
			$sql .= " ORDER BY $params[order] ";

		if(!empty($params['limit']))
			$sql .= " LIMIT ".$params['limit'];

		return $this->db->Execute($sql);
	} // Get

} // class::ResComment

