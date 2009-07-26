<?php

define('COMMENT_VISIBLE', 'Y');
define('COMMENT_INVISIBLE', 'N');
define('COMMENT_ALL', false);

class Comment
{
	var $db = null;

	function __construct() {
	} // __construct

	function setDb($db) {
		$this->db = $db;
	} // setDb

	function add($data)
	{
		$this->validate($data);

		$sql = "
INSERT INTO comment (
	c_userid, c_userlogin, c_username,
	c_useremail, c_data, c_datacompiled,
	c_visible, c_userip, c_entered
) VALUES (
	$data[c_userid], '$data[c_userlogin]', '$data[c_username]',
	'$data[c_useremail]', '$data[c_data]', '$data[c_datacompiled]',
	'$data[c_visible]', '$data[c_userip]', ".$this->db->now()."
)
";

		return $this->db->Execute($sql);
	} // add

	function get(Array $params = array())
	{
		$sql = "SELECT * FROM comment";

		$sql_add = array();

		if(!empty($params['c_id']))
			$sql_add[] = sprintf("(c_id = %d)", $params['c_id']);

		if(isset($params['c_visible']))
		{
			if($params['c_visible'])
				$sql_add[] = sprintf("c_visible = '%s'", $params['c_visible']);
		} else {
			$sql_add[] = sprintf("c_visible = '%s'", COMMENT_VISIBLE);
		}

		if($sql_add)
			$sql .= " WHERE ".join(' AND ', $sql_add);

		$sql .= " ORDER BY c_entered ";

		return (empty($params['c_id']) ? $this->db->Execute($sql) : $this->db->Executesingle($sql));
	} // get

	function validate(&$data)
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
			$data['c_visible'] = COMMENT_VISIBLE;

	} // validate

} // class::Comment

