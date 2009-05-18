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
define('COMMENT_VISIBLE', 'Y');
define('COMMENT_INVISIBLE', 'N');
define('COMMENT_ALL', false);

class Article {
	var $date_format;
	var $limit;
	var $error_msg;
	var $order;

	function Article() {
		//$this->date_format = '%d.%m.%Y %H:%i';
		$this->set_date_format('%Y:%m:%d:%H:%i');
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
		$sql = 'SELECT a.*, DATE_FORMAT(a.art_entered, \''.$this->date_format.'\') art_date'.$sql_add1.', m.* FROM article_'.$sys_lang.' a, modules_'.$sys_lang.' m';

		$sql_add .= 'a.art_modid = m.mod_id AND ';

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

	function load_by_userid($userid)
	{
		global $db, $sys_lang;

		$userid = (int)$userid;

		$sql = "SELECT * FROM article_$sys_lang, article_comments_$sys_lang WHERE ac_userid = $userid AND art_id = ac_artid";

		return $db->Execute($sql);
	} // load_by_userid

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
		INSERT INTO article_$sys_lang (
			art_name, art_username, art_useremail, art_userip, art_entered,
			art_modid, art_data, art_active, art_visible,
			art_comments, art_type
		) VALUES (
			'$data[art_name]', '$data[art_username]', '$data[art_useremail]', '$ip', ".$date.",
			$data[art_modid], '$data[art_data]', '$data[art_active]', '$data[art_visible]',
			'$data[art_comments]', '$data[art_type]'
		)";

		if($db->Execute($sql))
		{
			$this->update_cache($data['art_modid']);
			return last_insert_id();
		} else
			return false;
	}

	function comment_count($art_id, $ac_visible = COMMENT_VISIBLE)
	{
		global $db, $sys_lang;

		if(!$art_id)
			return 0;

		$sql1 = '';
		if($ac_visible)
			$sql1 =  "ac_visible='".COMMENT_VISIBLE."' AND ";

		$sql = "SELECT COUNT(*) comment_count FROM article_comments_$sys_lang WHERE $sql1 ac_artid = $art_id";

		$data = $db->ExecuteSingle($sql);

		return (integer)$data['comment_count'];
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
		$sql .= $data['art_data'] ? "art_data = '$data[art_data]', " : '';
		$sql .= "art_active = '$data[art_active]', ";
		$sql .= "art_visible = '$data[art_visible]', ";
		$sql .= "art_comments = '$data[art_comments]', ";
		$sql .= "art_type = '$data[art_type]', ";
		$sql .= "art_data = '$data[art_data]', ";
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE art_id = '.$art_id;

		if($db->Execute($sql))
		{
			$this->update_cache(0, $art_id);
		}

		return $art_id;
	}

	function save($art_id, &$data)
	{
		$this->validate($data);

		$art_id = (integer)$art_id;
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

		$this->update_cache(0, $art_id);

		$sql = 'DELETE FROM article_'.$sys_lang.' WHERE art_id = '.$art_id;

		return $db->Execute($sql);
	}

	function activate($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_active = "'.ARTICLE_ACTIVE.'" WHERE art_id = '.$art_id;

		$this->update_cache(0, $art_id);

		return $db->Execute($sql);
	}

	function deactivate($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_active = "'.ARTICLE_INACTIVE.'" WHERE art_id = '.$art_id;

		$this->update_cache(0, $art_id);
		return $db->Execute($sql);
	}

	function show($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_visible = "'.ARTICLE_VISIBLE.'" WHERE art_id = '.$art_id;

		$this->update_cache(0, $art_id);
		return $db->Execute($sql);
	}

	function hide($art_id)
	{
		global $db, $sys_lang;

		$art_id = (integer)$art_id;
		$sql = 'UPDATE article_'.$sys_lang.' SET art_visible = "'.ARTICLE_INVSIBLE.'" WHERE art_id = '.$art_id;

		$this->update_cache(0, $art_id);
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
					$this->update_cache(0, $data['art_id'.$r]);
				}
			}
		}

		return $ret;
	}

	function comment_del($ac_id = 0, $art_id = 0)
	{
		global $db, $sys_lang;

		$ac_id = (integer)$ac_id;
		$art_id = (integer)$art_id;

		if($art_id)
		{
			$sql = 'DELETE FROM article_comments_'.$sys_lang.' WHERE ac_artid = '.$art_id;
			$this->update_cache(0, $art_id);
		} elseif($ac_id) {
			$sql = 'DELETE FROM article_comments_'.$sys_lang.' WHERE ac_id = '.$ac_id;
			$this->update_cache(0, 0, $ac_id);
		} else {
			return false;
		}

		return $db->Execute($sql);
	}

	function comment_show($ac_id = 0, $art_id = 0)
	{
		global $db, $sys_lang;

		$ac_id = (integer)$ac_id;
		$art_id = (integer)$art_id;

		if($art_id)
		{
			$sql = 'UPDATE article_comments_'.$sys_lang.' SET ac_visible = "'.COMMENT_VISIBLE.'" WHERE ac_artid = '.$art_id;
			$this->update_cache(0, $art_id);
		} elseif($ac_id) {
			$sql = 'UPDATE article_comments_'.$sys_lang.' SET ac_visible = "'.COMMENT_VISIBLE.'" WHERE ac_id = '.$ac_id;
			$this->update_cache(0, 0, $ac_id);
		} else {
			return false;
		}

		return $db->Execute($sql);
	}

	function comment_hide($ac_id = 0, $art_id = 0)
	{
		global $db, $sys_lang;

		$ac_id = (integer)$ac_id;
		$art_id = (integer)$art_id;

		if($art_id)
		{
			$sql = 'UPDATE article_comments_'.$sys_lang.' SET ac_visible = "'.COMMENT_INVISIBLE.'" WHERE ac_artid = '.$art_id;
			$this->update_cache(0, $art_id);
		}

		if($ac_id)
		{
			$sql = 'UPDATE article_comments_'.$sys_lang.' SET ac_visible = "'.COMMENT_INVISIBLE.'" WHERE ac_id = '.$ac_id;
			$this->update_cache(0, 0, $ac_id);
		} else {
			return false;
		}

		return $db->Execute($sql);
	}

	// komentaaru actionu preprocessors
	function comment_process_action(&$data, $action)
	{
		$ret = true;
		$func = '';

		if($action == 'comment_delete_multiple')
			$func = 'comment_del';

		if($action == 'comment_show_multiple')
			$func = 'comment_show';

		if($action == 'comment_hide_multiple')
			$func = 'comment_hide';

		//if($action == 'comment_ban_multiple')
			//$func = 'comment_ban';

		if(isset($data['comment_count']) && $func)
		{
			for($r = 1; $r <= $data['comment_count']; ++$r)
			{
				// ja iechekots, proceseejam
				if(isset($data['comment_checked'.$r]) && isset($data['ac_id'.$r]))
				{
					$ret = $ret && $this->{$func}($data['ac_id'.$r]);
					$this->update_cache(0, 0, $data['ac_id'.$r]);
				}
			}
		}

		return $ret;
	}

	function load_comment($ac_id, $ac_visible = COMMENT_VISIBLE)
	{
		global $db, $sys_lang;

		$sql1 = '';
		if($ac_visible)
			$sql1 =  "ac.ac_visible='".COMMENT_VISIBLE."' AND ";

		$sql = "SELECT ac.*, DATE_FORMAT(ac.ac_entered, '".$this->date_format."') ac_date FROM article_comments_$sys_lang ac WHERE $sql1 ac_id = $ac_id";

		return $db->ExecuteSingle($sql);
	} // load_comment

	function load_comments($art_id, $ac_visible = COMMENT_VISIBLE)
	{
		global $db, $sys_lang;

		$sql1 = '';
		if($ac_visible)
			$sql1 =  "ac.ac_visible='".COMMENT_VISIBLE."' AND ";

		$sql = "SELECT ac.*, DATE_FORMAT(ac.ac_entered, '".$this->date_format."') ac_date FROM article_comments_$sys_lang ac WHERE $sql1 ac_artid = $art_id ORDER BY ac.ac_entered ";

		return $db->Execute($sql);
	}

	function add_comment($art_id, &$data, $validate = ARTICLE_VALIDATE)
	{
		global $ip, $db, $sys_lang;

		if(!user_loged())
		{
			$this->error_msg = 'Nav ielogojies!';
			return false;
		}

		if(!ereg('[0-9]', $art_id))
			return false;

		$ban = new Ban;
		if($ban_info = $ban->banned($ip, 'article'))
		{
			$this->error_msg = 'Banned - '.$ban_info['ub_reason'];
			return false;
		}

		if($validate)
			$this->validate_comment($data);

		$user_id = $_SESSION['login']['l_id'];
		$user_login = $_SESSION['login']['l_login'];

		$sql = "
		INSERT INTO article_comments_$sys_lang (
			ac_artid, ac_userid, ac_userlogin, ac_username,
			ac_useremail, ac_data, ac_datacompiled,
			ac_userip, ac_entered
		) VALUES (
			$art_id, $user_id, '$user_login', '$data[ac_username]',
			'$data[ac_useremail]', '$data[ac_data]', '$data[ac_datacompiled]',
			'$ip', ".$db->now()."
		)";

		if($db->Execute($sql))
		{
			$_SESSION['user']['username'] = $data['ac_username'];
			$_SESSION['user']['useremail'] = $data['ac_useremail'];
			$this->update_cache(0, $art_id);
			return last_insert_id();
		} else
			return false;
	}

	function validate_comment(&$data)
	{
		if(!isset($data['ac_username']))
			$data['ac_username'] = '';

		if(!isset($data['ac_useremail']))
			$data['ac_useremail'] = '';

		if(!isset($data['ac_data']))
			$data['ac_data'] = '';

		if(!isset($data['ac_datacompiled']))
			$data['ac_datacompiled'] = $data['ac_data'];

		if(!isset($data['ac_entered']))
			$data['ac_entered'] = '';

		if(isset($data['ac_visible']))
			$data['ac_visible'] = ereg('[^YN]', $data['ac_visible']) ? '' : $data['ac_visible'];
		else
			$data['ac_visible'] = COMMENT_VISIBLE;

		parse_text_data($data['ac_datacompiled']);
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

		if(!isset($data['editor_data']))
			$data['editor_data'] = '';

		$data['art_data'] = &$data['editor_data'];

		if(!isset($data['art_entered']))
			$data['art_entered'] = '';

		my_strip_tags($data['art_name']);
		my_strip_tags($data['art_username']);
		my_strip_tags($data['art_useremail']);

	}

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
			$comment_count = $this->comment_count($item['art_id']);
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

	function get_total($art_modid = 0)
	{
		global $db, $sys_lang;

		$sql_add = '';
		$sql = "SELECT COUNT(*) art_count FROM article_$sys_lang a";
		if($art_modid)
			$sql_add .= "a.art_modid = $art_modid AND ";

		$sql_add = substr($sql_add, 0, -4);

		if($sql_add)
			$sql .= " WHERE $sql_add";

		$data = $db->ExecuteSingle($sql);

		return $data['art_count'];
	} // get_total
}

