<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

class Comment extends Res
{
	const VISIBLE = 'Y';
	const INVISIBLE = 'N';
	const ALL = false;

	protected $table_id = Table::COMMENT;

	function __construct() {
		parent::__construct();
	} // __construct

	function Add($data)
	{
		if(!($res_id = parent::Add())) {
			return false;
		}

		$this->validate($data);

		$sql = "
INSERT INTO comment (
	res_id, login_id, c_userlogin, c_username,
	c_useremail, c_data, c_datacompiled,
	c_visible, c_userip, c_entered
) VALUES (
	$res_id, $data[login_id], '$data[c_userlogin]', '$data[c_username]',
	'$data[c_useremail]', '$data[c_data]', '$data[c_datacompiled]',
	'$data[c_visible]', '$data[c_userip]', ".$this->db->now()."
)
";

		return $this->db->Execute($sql);
	} // Add

	function Get(Array $params = array())
	{
		$sql = "
SELECT
	comment.*,
	r.res_votes
FROM comment
JOIN res r ON r.res_id = comment.res_id
";

		$sql_add = array();

		if(!empty($params['c_id']))
			$sql_add[] = sprintf("(c_id = %d)", $params['c_id']);

		if(isset($params['c_visible']))
		{
			if($params['c_visible'])
				$sql_add[] = sprintf("c_visible = '%s'", $params['c_visible']);
		} else {
			$sql_add[] = sprintf("c_visible = '%s'", Comment::VISIBLE);
		}

		if($sql_add)
			$sql .= " WHERE ".join(' AND ', $sql_add);

		if(empty($params['sort']))
			$sql .= " ORDER BY c_entered ";
		else
			$sql .= " ORDER BY $params[sort] ";

		if(!empty($params['limit']))
			$sql .= " LIMIT ".$params['limit'];

		return (empty($params['c_id']) ? $this->db->Execute($sql) : $this->db->Executesingle($sql));
	} // Get

	function Delete($id)
	{
		$sql = sprintf("DELETE FROM `comment` WHERE c_id = %d", $id);

		return $this->db->Execute($sql);
	} // Delete

	function Show($id)
	{
		$sql = sprintf(
			"UPDATE `comment` SET c_visible = '%s' WHERE c_id = %d",
			Comment::VISIBLE,
			$id
			);

		return $this->db->Execute($sql);
	} // Show

	function Hide($id)
	{
		$sql = sprintf(
			"UPDATE `comment` SET c_visible = '%s' WHERE c_id = %d",
			Comment::INVISIBLE,
			$id
			);

		return $this->db->Execute($sql);
	} // Hide

	function Validate(&$data)
	{
		if(!isset($data['c_username']))
			$data['c_username'] = '';

		if(!isset($data['c_useremail']))
			$data['c_useremail'] = '';

		if(!isset($data['c_data']))
			$data['c_data'] = '';

		$data['c_datacompiled'] = $data['c_data'];
		parse_text_data($data['c_datacompiled']);

		if(isset($data['c_visible']))
			$data['c_visible'] = ereg('[^YN]', $data['c_visible']) ? '' : $data['c_visible'];
		else
			$data['c_visible'] = Comment::VISIBLE;

	} // Validate

} // class::Comment

