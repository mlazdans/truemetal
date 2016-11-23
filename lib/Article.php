<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Module.php');
require_once('lib/Res.php');
require_once('lib/Table.php');

define('ARTICLE_ACTIVE', 'Y');
define('ARTICLE_INACTIVE', 'N');
define('ARTICLE_ALL', false);
define('ARTICLE_VALIDATE', true);
define('ARTICLE_DONTVALIDATE', false);
define('ARTICLE_COMMENTS', 'Y');
define('ARTICLE_NOCOMMENTS', 'N');
define('ARTICLE_TYPE_OPEN', 'O');
define('ARTICLE_TYPE_REGISTRATED', 'R');

class Article extends Res
{
	var $date_format;
	var $limit;
	var $error_msg;
	var $order;

	protected $table_id = Table::ARTICLE;

	function __construct()
	{
		global $db;

		parent::__construct();

		$this->SetDb($db);
	} // __construct

	function set_date_format($new_date)
	{
		$this->date_format = $new_date;
	} // set_date_format

	function load(Array $params = array())
	{
		//global $db;

		$sql_add = array();

		if(isset($params['art_id']))
			$sql_add[] = sprintf("art_id = %d", $params['art_id']);

		if(isset($params['res_id']))
			$sql_add[] = sprintf("a.res_id = %d", $params['res_id']);

		if(isset($params['art_ids']) && is_array($params['art_ids']))
			$sql_add[] = sprintf("art_id IN (%s)", join(",", $params['art_ids']));

		if(isset($params['res_ids']) && is_array($params['res_ids']))
			$sql_add[] = sprintf("a.res_id IN (%s)", join(",", $params['res_ids']));

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
	m.*,
	r.*,
	COALESCE(res_comment_count, 0) AS art_comment_count,
	res_comment_lastdate AS art_comment_lastdate
FROM
	`article` a
JOIN `modules` m ON (a.art_modid = m.mod_id)
JOIN `res` r ON r.`res_id` = a.`res_id`
";

		if($sql_add)
			$sql .= " WHERE ".join(" AND ", $sql_add);

		$sql .= (empty($params['order']) ? " ORDER BY art_entered DESC " : " ORDER BY $params[order] ");

		if(isset($params['limit']))
			$sql .= " LIMIT $params[limit]";

		return (isset($params['art_id']) || isset($params['res_id']) ? $this->db->ExecuteSingle($sql) : $this->db->Execute($sql));
	} // load

	function insert(&$data, $validate = ARTICLE_VALIDATE)
	{
		global $ip;

		$this->login_id = $login_id = 3;
		if(!($res_id = parent::Add())) {
			return false;
		}

		if($validate)
			$this->validate($data);

		$date = $this->db->now();
		if($data['art_entered'])
			$date = "'$data[art_entered]'";

		$data2 = $this->db->QuoteArray($data);
		//$login_id = (int)(isset($_SESSION['login']['l_id']) ? $_SESSION['login']['l_id'] : 0);

		$sql = "
INSERT INTO article (
	res_id, art_name, art_username, art_useremail, art_userip, art_entered,
	art_modid, art_data, art_intro, art_active,
	art_comments, art_type, login_id
) VALUES (
	$res_id, '$data2[art_name]', '$data2[art_username]', '$data2[art_useremail]', '$ip', ".$date.",
	$data2[art_modid], '$data2[art_data]', '$data2[art_intro]', '$data2[art_active]',
	'$data2[art_comments]', '$data2[art_type]', $login_id
)";

		return ($this->db->Execute($sql) ? $this->db->LastID() : false);
	}

	function update($art_id, &$data, $validate = ARTICLE_VALIDATE)
	{
		//global $db;

		$art_id = (integer)$art_id;
		if(!$art_id)
		{
			$this->error_msg = 'Nav norādīts vai nepareizs raksta ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$data2 = $this->db->QuoteArray($data);

		$sql = 'UPDATE article SET ';
		$sql .= $data2['art_name'] ? "art_name = '$data2[art_name]', " : '';
		$sql .= $data2['art_entered'] ? "art_entered = '$data2[art_entered]', " : '';
		$sql .= "art_active = '$data2[art_active]', ";
		$sql .= "art_comments = '$data2[art_comments]', ";
		$sql .= "art_type = '$data2[art_type]', ";
		$sql .= "art_data = '$data2[art_data]', ";
		$sql .= "art_intro = '$data2[art_intro]', ";
		if(!empty($data2['art_modid']))
			$sql .= "art_modid = $data2[art_modid], ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE art_id = '.$art_id;

		return ($this->db->Execute($sql) ? $art_id : false);
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
		//global $db;

		if(!$art_id)
		{
			return true;
		}

		$sql = 'DELETE FROM `article` WHERE art_id = '.$art_id;

		return $this->db->Execute($sql);
	}

	function activate($art_id)
	{
		//global $db;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE `article` SET art_active = "'.ARTICLE_ACTIVE.'" WHERE art_id = '.$art_id;

		return $this->db->Execute($sql);
	}

	function deactivate($art_id)
	{
		//global $db;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE `article` SET art_active = "'.ARTICLE_INACTIVE.'" WHERE art_id = '.$art_id;

		return $this->db->Execute($sql);
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
			$data['art_modid'] = preg_match('/[0-9]/', $data['art_modid']) ? (int)$data['art_modid'] : 0;
		else
			$data['art_modid'] = 0;

		if(isset($data['art_modid']))
			$data['art_modid'] = preg_match('/[0-9]/', $data['art_modid']) ? (int)$data['art_modid'] : 0;
		else
			$data['art_modid'] = 0;

		if(isset($data['art_active']))
			$data['art_active'] = preg_match('/[YN]/', $data['art_active']) ? $data['art_active'] : ARTICLE_ACTIVE;
		else
			$data['art_active'] = ARTICLE_ACTIVE;

		if(isset($data['art_comments']))
			$data['art_comments'] = preg_match('/[YN]/', $data['art_comments']) ? $data['art_comments'] : ARTICLE_COMMENTS;
		else
			$data['art_comments'] = ARTICLE_COMMENTS;

		if(isset($data['art_type']))
			$data['art_type'] = preg_match('/[OR]/', $data['art_type']) ? $data['art_type'] : ARTICLE_TYPE_OPEN;
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
		//global $db;

		$sql_add = '';
		$sql = "SELECT COUNT(*) art_count FROM `article` a";
		if($art_modid)
			$sql_add .= "a.art_modid = $art_modid AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= " WHERE $sql_add";

		$data = $this->db->ExecuteSingle($sql);

		return $data['art_count'];
	} // get_total

	static function hasNewComments($item)
	{
		if(isset($_SESSION['comments']['viewed'][$item['art_id']]))
			return ($item['art_comment_count'] > $_SESSION['comments']['viewed'][$item['art_id']]);

		if(isset($_SESSION['comments']['viewed_before']))
			return ($_SESSION['comments']['viewed_before'] < strtotime($item['art_comment_lastdate']));

		return true;
	} // hasNewComments

} // class::Article

