<?php

require_once('lib/Comment.php');

class CommentConnect
{
	var $table = '';
	var $db = null;

	function __construct($table)
	{
		$this->table = $table;
	} // __construct

	function setDb($db)
	{
		$this->db = $db;
		$this->db->AutoCommit(false);
	} // setDb

	function connect($table_id, $c_id)
	{
		$sql = "
INSERT INTO `comment_connect` (
	`cc_table`, `cc_table_id`, `cc_c_id`
) VALUES (
	'$this->table', '$table_id', '$c_id'
)
";

		return $this->db->Execute($sql);
	} // connect

	function add($table_id, $data)
	{
		if($this->db == null)
			$this->_db_connect();

		$Comment = new Comment();
		$Comment->setDb($this->db);
		if(!$Comment->add($data))
		{
			$this->db->Rollback();
			return false;
		}

		$c_id = $this->db->LastID();
		$c_ok = $this->connect($table_id, $c_id);

		if($c_ok) {
			//$this->db->Commit();
			return $c_id;
		} else {
			//$this->db->Rollback();
			return false;
		}
	} // add

	function get(Array $params = array())
	{
		if($this->db == null)
			$this->_db_connect();

		$sql = "
SELECT
	comment.*
FROM
	comment
JOIN comment_connect ON cc_c_id = c_id
WHERE
	cc_table = '$this->table'";

		$sql_add = array();

		if(!empty($params['cc_table_id']))
			$sql_add[] = sprintf("(cc_table_id = %d)", $params['cc_table_id']);

		if(isset($params['c_visible']))
		{
			if($params['c_visible'])
				$sql_add[] = sprintf("c_visible = '%s'", $params['c_visible']);
		} else {
			$sql_add[] = sprintf("c_visible = '%s'", COMMENT_VISIBLE);
		}

		if($sql_add)
			$sql .= " AND ".join(' AND ', $sql_add);

		if(empty($params['sort']))
			$sql .= " ORDER BY c_entered ";
		else
			$sql .= " ORDER BY $params[sort] ";

		return $this->db->Execute($sql);
	} // get

	protected function _db_connect()
	{
		global $sys_database_type, $sys_db_host, $sys_db_user, $sys_db_password,
		$sys_db_name, $sys_db_port;

		$this->db = new SQLLayer($sys_database_type);
		$this->db->Connect($sys_db_host, $sys_db_user, $sys_db_password, $sys_db_name, $sys_db_port);
		$this->db->AutoCommit(false);
	} // _db_connect

} // class::CommentConnect

