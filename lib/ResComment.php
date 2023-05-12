<?php declare(strict_types = 1);

class ResComment extends Res
{
	function Connect($res_id, $c_id)
	{
		$sql = sprintf(
			"INSERT INTO res_comment (res_id, c_id) VALUES ('%d', '%d')",
			$res_id,
			$c_id
			);

		return DB::Execute($sql);
	}

	function Move($id, $res_id)
	{
		$sql = sprintf(
			"UPDATE `res_comment` SET res_id = %d WHERE c_id = %d",
			$res_id,
			$id
			);

		return DB::Execute($sql);
	}

	function Add()
	{
		list($res_id, $data) = func_get_args();

		// $this->InitDb();

		$Comment = new Comment();
		// $Comment->setDb($this->db);
		if(!$Comment->Add($data)) {
			return false;
		}

		$c_id = DB::LastID();

		return ($this->Connect($res_id, $c_id) ? $c_id : false);
	}

	function Get(Array $params = array())
	{
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
	r.res_votes_plus_count,
	r.res_votes_minus_count,
	r2.res_id AS parent_res_id,
	r2.table_id AS parent_table_id,
	l.l_login,
	l.l_hash
FROM
	res_comment rc
JOIN comment c ON c.c_id = rc.c_id
JOIN res r ON r.res_id = c.res_id
JOIN res r2 ON r2.res_id = rc.res_id
LEFT JOIN logins l ON l.l_id = r.login_id
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
			return DB::ExecuteSingle($sql);
		} else {
			return DB::Execute($sql);
		}
	}

}
