<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/Module.php');

define('ARTICLE_ACTIVE', 'Y');
define('ARTICLE_INACTIVE', 'N');
define('ARTICLE_ALL', false);
define('ARTICLE_VALIDATE', true);
define('ARTICLE_DONTVALIDATE', false);
define('ARTICLE_COMMENTS', 'Y');
define('ARTICLE_NOCOMMENTS', 'N');
define('ARTICLE_TYPE_OPEN', 'O');
define('ARTICLE_TYPE_REGISTRATED', 'R');

class Article {
	var $date_format;
	var $limit;
	var $error_msg;
	var $order;

	function __construct() {
	} // __construct

	function set_date_format($new_date)
	{
		$this->date_format = $new_date;
	} // set_date_format

	function load(Array $params = array())
	{
		global $db;

		$sql_add = array();

		if(isset($params['art_id']))
			$sql_add[] = sprintf("art_id = %d", $params['art_id']);

		if(isset($params['art_ids']) && is_array($params['art_ids']))
			$sql_add[] = sprintf("art_id IN (%s)", join(",", $params['art_ids']));

		if(isset($params['art_active']))
		{
			if($params['art_active'])
				$sql_add[] = sprintf("art_active = '%s'", $params['art_active']);
		} else {
			$sql_add[] = sprintf("art_active = '%s'", ARTICLE_ACTIVE);
		}

		if(isset($params['art_modid']))
			$sql_add[] = sprintf("art_modid = %d", $params['art_modid']);

		if(isset($params['end_date']))
			$sql_add[] = sprintf("art_entered <= '%s'", $params['end_date']);

		$sql = "
SELECT
	a.*,
	DATE_FORMAT(a.art_entered, '$this->date_format') art_date,
	m.*,
	COALESCE(cm_comment_count, 0) AS art_comment_count,
	cm_comment_lastdate AS art_comment_lastdate
FROM
	`article` a
JOIN `modules` m ON (a.art_modid = m.mod_id)
LEFT JOIN `comment_meta` ON (cm_table = 'article') AND (cm_table_id = a.art_id)
";

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= (empty($params['order']) ? " ORDER BY art_entered DESC " : " ORDER BY $params[order] ");

		if(isset($params['limit']))
			$sql .= " LIMIT $params[limit]";

		return (isset($params['art_id']) ? $db->ExecuteSingle($sql) : $db->Execute($sql));
	}

	function insert(&$data, $validate = ARTICLE_VALIDATE)
	{
		global $db, $ip;

		if($validate)
			$this->validate($data);

		$date = $db->now();
		if($data['art_entered'])
			$date = "'$data[art_entered]'";

		$sql = "
INSERT INTO article (
	art_name, art_username, art_useremail, art_userip, art_entered,
	art_modid, art_data, art_intro, art_active,
	art_comments, art_type
) VALUES (
	'$data[art_name]', '$data[art_username]', '$data[art_useremail]', '$ip', ".$date.",
	$data[art_modid], '$data[art_data]', '$data[art_intro]', '$data[art_active]',
	'$data[art_comments]', '$data[art_type]'
)";

		return ($db->Execute($sql) ? $db->LastID() : false);
	}

	function update($art_id, &$data, $validate = ARTICLE_VALIDATE)
	{
		global $db, $ip;

		$art_id = (integer)$art_id;
		if(!$art_id)
		{
			$this->error_msg = 'Nav norādīts vai nepareizs raksta ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE article SET ';
		$sql .= $data['art_name'] ? "art_name = '$data[art_name]', " : '';
		$sql .= $data['art_entered'] ? "art_entered = '$data[art_entered]', " : '';
		$sql .= "art_active = '$data[art_active]', ";
		$sql .= "art_comments = '$data[art_comments]', ";
		$sql .= "art_type = '$data[art_type]', ";
		$sql .= "art_data = '$data[art_data]', ";
		$sql .= "art_intro = '$data[art_intro]', ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE art_id = '.$art_id;

		return ($db->Execute($sql) ? $art_id : false);
	}

	function save($art_id, &$data)
	{
		$this->validate($data);

		$art_id = (int)$art_id;
		$error_msg = '';

		if(!$data['art_modid'])
			$error_msg .= 'Nav norādīts vai nepareizs moduļa ID<br>';

		if(!$data['art_name'])
			$error_msg .= 'Nav norādīts ziņas nosaukums<br>';

		if(!$error_msg)
		{
			if($art_id)
				return $this->update($art_id, $data, ARTICLE_DONTVALIDATE);
			else
				return $this->insert($data, ARTICLE_DONTVALIDATE);
		} else { // $error_msg
			$this->error_msg = $error_msg;
			return false;
		}
	}

	function del($art_id)
	{
		global $db;

		if(!$art_id)
		{
			return true;
		}

		$sql = 'DELETE FROM `article` WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function activate($art_id)
	{
		global $db;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE `article` SET art_active = "'.ARTICLE_ACTIVE.'" WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function deactivate($art_id)
	{
		global $db;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE `article` SET art_active = "'.ARTICLE_INACTIVE.'" WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	// actionu preprocessors
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

		if(isset($data['article_count']) && $func)
		{
			for($r = 1; $r <= $data['article_count']; ++$r)
			{
				// ja iechekots, proceseejam
				if(isset($data['art_checked'.$r]) && isset($data['art_id'.$r]))
				{
					$ret = $ret && $this->{$func}($data['art_id'.$r]);
				}
			}
		}

		return $ret;
	}

	function validate(&$data)
	{
		if(isset($data['art_modid']))
			$data['art_modid'] = !ereg('[0-9]', $data['art_modid']) ? 0 : $data['art_modid'];
		else
			$data['art_modid'] = 0;

		if(isset($data['art_modid']))
			$data['art_modid'] = !ereg('[0-9]', $data['art_modid']) ? 0 : $data['art_modid'];
		else
			$data['art_modid'] = 0;

		if(isset($data['art_active']))
			$data['art_active'] = ereg('[^YN]', $data['art_active']) ? '' : $data['art_active'];
		else
			$data['art_active'] = ARTICLE_ACTIVE;

		if(isset($data['art_comments']))
			$data['art_comments'] = ereg('[^YN]', $data['art_comments']) ? '' : $data['art_comments'];
		else
			$data['art_comments'] = ARTICLE_COMMENTS;

		if(isset($data['art_type']))
			$data['art_type'] = ereg('[^OR]', $data['art_type']) ? '' : $data['art_type'];
		else
			$data['art_type'] = ARTICLE_TYPE_OPEN;

		if(!isset($data['art_name']))
			$data['art_name'] = '';

		if(!isset($data['art_username']))
			$data['art_username'] = '';

		if(!isset($data['art_useremail']))
			$data['art_useremail'] = '';

		if(!isset($data['art_data']))
			$data['art_data'] = '';

		if(!isset($data['art_intro']))
			$data['art_intro'] = '';

		if(!isset($data['art_entered']))
			$data['art_entered'] = '';

		my_strip_tags($data['art_name']);
		my_strip_tags($data['art_username']);
		my_strip_tags($data['art_useremail']);

	} // validate

	function get_total($art_modid = 0)
	{
		global $db;

		$sql_add = '';
		$sql = "SELECT COUNT(*) art_count FROM `article` a";
		if($art_modid)
			$sql_add .= "a.art_modid = $art_modid AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= " WHERE $sql_add";

		$data = $db->ExecuteSingle($sql);

		return $data['art_count'];
	} // get_total
}

