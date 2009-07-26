<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../classes/class.Ban.php');
require_once('../classes/class.Module.php');

define('ARTICLE_ACTIVE', 'Y');
define('ARTICLE_INACTIVE', 'N');
define('ARTICLE_VISIBLE', 'Y');
define('ARTICLE_INVISIBLE', 'N');
define('ARTICLE_ALL', false);
define('ARTICLE_LIMIT', 10);
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

	function Article() {
		//$this->date_format = '%d.%m.%Y %H:%i';
		//$this->set_date_format('%Y:%m:%d:%H:%i');
		$this->set_limit(ARTICLE_LIMIT);
	}

	function set_order($order)
	{
		$this->order = $order;
	} // set_order

	function set_date_format($new_date)
	{
		$this->date_format = $new_date;
	} // set_date_format

	function set_limit($limit)
	{
		$this->limit = $limit;
	}

	function load($art_id = 0, $art_modid = 0, $art_active = ARTICLE_ACTIVE,
		$art_visible = ARTICLE_VISIBLE, $end_date = '', $q = '', $art_type = ARTICLE_ALL
	) {
		global $db, $sys_lang;

		if($q)
			$search = search_to_sql($q, array('art_name', 'art_data'));

		$art_id = (integer)$art_id;
		if($q)
			$sql_add1 = ", $search score";
		else
			$sql_add1 = '';

		$sql_add = '';
		//(SELECT COUNT(c_id) FROM comment JOIN comment_connect ON cc_c_id = c_id WHERE cc_table = '$art_table' AND cc_table_id = a.art_id) art_comment_count,
		$sql = "
SELECT
	a.*,
	DATE_FORMAT(a.art_entered, '$this->date_format') art_date $sql_add1,
	m.*,
	COALESCE(cm_comment_count, 0) AS art_comment_count,
	cm_comment_lastdate AS art_comment_lastdate
FROM
	`article` a
JOIN modules_$sys_lang m ON (a.art_modid = m.mod_id)
LEFT JOIN comment_meta ON (cm_table = 'article') AND (cm_table_id = a.art_id)
";

		//$sql_add .= ' ';

		if($q)
			$sql_add .= "$search AND ";

		if($art_id)
			$sql_add .= "art_id = $art_id AND ";

		if($art_active)
			$sql_add .= "art_active = '$art_active' AND ";

		if($end_date)
			$sql_add .= "art_entered <= '$end_date' AND ";

		if($art_visible)
			$sql_add .= "art_visible = '$art_visible' AND ";

		if($art_modid)
			$sql_add .= "art_modid = $art_modid AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= ' WHERE '.$sql_add;

		if(isset($this->order) && $this->order)
		{
			$sql .= ' ORDER BY '.$this->order;
		} elseif(isset($this->limit) && $this->limit) {
			$sql .= ' ORDER BY art_entered DESC LIMIT '.$this->limit;
		}

		if($art_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	}

/*
	function load_by_userid($userid)
	{
		global $db, $sys_lang;

		$userid = (int)$userid;

		$sql = "SELECT * FROM article_$sys_lang, article_comments_$sys_lang WHERE ac_userid = $userid AND art_id = ac_artid";

		return $db->Execute($sql);
	} // load_by_userid
*/

	function load_under(&$module_tree)
	{
		if(!isset($module_tree['_data_']))
			return array();

		$ret = $this->load(0, $module_tree['_data_']['mod_id']);
		foreach($module_tree as $k=>$module)
		{
			//if($k == '_data_')
				//continue;
			$ret = array_merge($ret, $this->load_under($module));
		}

		return $ret;
	} // load_under

	function load_date($end_date)
	{
		$this->set_limit(50);
		return $this->load(0, 0, ARTICLE_ACTIVE, ARTICLE_VISIBLE, $end_date);
	} // load_date

	function insert(&$data, $validate = ARTICLE_VALIDATE)
	{
		global $db, $ip, $sys_lang;

		//$mod_id = (integer)$mod_id;

		if($validate)
			$this->validate($data);

		$date = $db->now();
		if($data['art_entered'])
			$date = "'$data[art_entered]'";

		$sql = "
		INSERT INTO article (
			art_name, art_username, art_useremail, art_userip, art_entered,
			art_modid, art_data, art_intro, art_active, art_visible,
			art_comments, art_type
		) VALUES (
			'$data[art_name]', '$data[art_username]', '$data[art_useremail]', '$ip', ".$date.",
			$data[art_modid], '$data[art_data]', '$data[art_intro]', '$data[art_active]', '$data[art_visible]',
			'$data[art_comments]', '$data[art_type]'
		)";

		return ($db->Execute($sql) ? $db->LastID() : false);
	}

	function update($art_id, &$data, $validate = ARTICLE_VALIDATE)
	{
		global $db, $ip, $sys_lang;

		$art_id = (integer)$art_id;
		if(!$art_id)
		{
			$this->error_msg = 'Nav norādīts vai nepareizs raksta ID<br>';
			return false;
		}

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE article_'.$sys_lang.' SET ';
		$sql .= $data['art_name'] ? "art_name = '$data[art_name]', " : '';
		$sql .= $data['art_entered'] ? "art_entered = '$data[art_entered]', " : '';
		//$sql .= $data['art_data'] ? "art_data = '$data[art_data]', " : '';
		//$sql .= $data['art_intro'] ? "art_intro = '$data[art_intro]', " : '';
		$sql .= "art_active = '$data[art_active]', ";
		$sql .= "art_visible = '$data[art_visible]', ";
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
		global $db, $sys_lang;

		if(!$art_id)
		{
			return true;
		}

		$sql = 'DELETE FROM article_'.$sys_lang.' WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function activate($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_active = "'.ARTICLE_ACTIVE.'" WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function deactivate($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_active = "'.ARTICLE_INACTIVE.'" WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function show($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_visible = "'.ARTICLE_VISIBLE.'" WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function hide($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_visible = "'.ARTICLE_INVSIBLE.'" WHERE art_id = '.$art_id;

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

		if($action == 'show_multiple')
			$func = 'show';

		if($action == 'hide_multiple')
			$func = 'hide';

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

		if(isset($data['art_visible']))
			$data['art_visible'] = ereg('[^YN]', $data['art_visible']) ? '' : $data['art_visible'];
		else
			$data['art_visible'] = ARTICLE_VISIBLE;

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

		//$data['art_data'] = &$data['editor_data'];

		if(!isset($data['art_entered']))
			$data['art_entered'] = '';

		my_strip_tags($data['art_name']);
		my_strip_tags($data['art_username']);
		my_strip_tags($data['art_useremail']);

	} // validate
/*
	function search($q)
	{
		$this->set_limit(50);
		$data = $this->load(0, 0, ARTICLE_ACTIVE, ARTICLE_VISIBLE, '', $q);
		return $data;
	}

	function update_cache($mod_id = 0, $art_id = 0, $ac_id = 0)
	{
		global $db, $sys_lang;

		if(!$mod_id && !$art_id && $ac_id)
		{
			if($comment = $db->ExecuteSingle("SELECT * FROM article_comments_$sys_lang WHERE ac_id = $ac_id"))
			{
				$art_id = $comment['ac_artid'];
			} else {
				return false;
			}
		}

		if(!$mod_id && $art_id)
		{
			if($article = $this->load($art_id))
			{
				$mod_id = $article['art_modid'];
			} else {
				return false;
			}
		}

		$module = new Module;
		$module->load($mod_id);
		if(isset($module->data[$mod_id]['module_id']))
		{
			update_cache_module($module->data[$mod_id]['module_id']);
		}
	} // update_cache

	function set_comment_count(&$template, &$articles)
	{
		$template->parse_block('FILE_article');
		$template->create_file('FILE_tmp', $template->get_parsed_content('FILE_article'));

		reset($articles);
		foreach($articles as $item)
		{
			//$comment_count = $this->comment_count($item['art_id']);
			$comment_count = $item['art_comment_count'];
			$old_comment_count =
				isset($_SESSION['comments']['viewed'][$item['art_id']]) ?
				$_SESSION['comments']['viewed'][$item['art_id']] :
				0;

			if($comment_count)
			{
				if($comment_count > $old_comment_count)
				{
					$template->set_var('comment_style_'.$item['art_id'], ' color: red;', 'FILE_tmp', true);
				} else {
					$template->set_var('comment_style_'.$item['art_id'], '', 'FILE_tmp', true);
				}
			}

			$template->set_var('comment_count_'.$item['art_id'], $comment_count, 'FILE_tmp', true);
		}
		$template->parse_block('FILE_tmp');
		$template->set_block_string('BLOCK_middle', $template->get_parsed_content('FILE_tmp'));

		$template->delete_block('FILE_tmp');
	} // set_comment_count
*/

	function get_total($art_modid = 0)
	{
		global $db, $sys_lang;

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

