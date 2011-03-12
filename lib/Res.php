<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

class Res
{
	protected $table_id;
	protected $db = null;

	function __construct() {
	} // __construct

	function Get(Array $params = array())
	{
		$this->InitDb();

		$sql = "SELECT * FROM `res`";

		$sql_add = array();

		if(!empty($params['res_id']))
			$sql_add[] = sprintf("res_id = %d", $params['res_id']);

		if($sql_add)
			$sql .= " WHERE ".join(' AND ', $sql_add);

		if(!empty($params['order']))
			$sql .= " ORDER BY $params[order] ";

		if(!empty($params['limit']))
			$sql .= " LIMIT ".$params['limit'];

		return (empty($params['res_id']) ? $this->db->Execute($sql) : $this->db->ExecuteSingle($sql));
	} // Get

	function Add()
	{
		$this->InitDb();

		$sql = sprintf(
			"INSERT INTO `res` (`table_id`, `res_entered`) VALUES (%s, %s);",
			($this->table_id ? $this->table_id : "NULL"),
			$this->db->now()
			);

		return ($this->db->Execute($sql) ? $this->db->LastID() : false);
	} // Add

	function Commit() {
		$this->db->Commit();
	} // Commit

	function Rollback() {
		$this->db->Rollback();
	} // Rollback

	function SetDb($db)
	{
		$this->db = $db;
	} // SetDb

	function GetAllData($res_id)
	{
		$res_data = $this->Get(array(
			'res_id'=>$res_id,
			));

		if(!$res_data) {
			return false;
		}

		switch($res_data['table_id'])
		{
			case Table::ARTICLE:
				require_once("lib/Article.php");
				$D = new Article();
				return $D->load(array(
					'res_id'=>$res_data['res_id'],
					));
			case Table::FORUM:
				require_once("lib/Forum.php");
				$D = new Forum();
				return $D->load(array(
					'res_id'=>$res_data['res_id'],
					));
				break;
			case Table::COMMENT:
				require_once("lib/Comment.php");
				$D = new Comment();
				return $D->Get(array(
					'res_id'=>$res_data['res_id'],
					));
				break;
		}

		return false;
	} // GetAllData

	protected function InitDb()
	{
		if(!$this->db)
		{
			require('include/dbconnect.php');
			$this->db = $db;
			$this->db->AutoCommit(false);
		}
	} // InitDb

/*
	public static function genId()
	{
		if(empty(Res::$db)) {
			require('include/dbconnect.php');
			Res::$db = $db;
		}

		Res::$db->Execute("UPDATE `resid` SET `id` = LAST_INSERT_ID(`id` + 1)");
		$resId = Res::$db->LastID();

		return $resId;
	} // genId
*/

} // class::Res

