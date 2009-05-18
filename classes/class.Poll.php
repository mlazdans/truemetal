<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// 

require_once('../classes/class.Ban.php');
require_once('../classes/class.Permission.php');

define('POLL_ACTIVE', 'Y');
define('POLL_INACTIVE', 'N');
define('POLL_ALL', false);
define('POLL_VALIDATE', true);
define('POLL_DONTVALIDATE', false);

class Poll
{
	var $date_format;
	var $error_msg;
	var $insert_period; // sec
	var $insert_rate; // rate = $insert_rate / $insert_period
	var $permissions;

	function Poll()
	{
		global $_USER;

		$this->date_format = '%d.%m.%Y %H:%i';
		$this->set_insert_rate(5, 4); // max 5msg in 4sec

		$perm = new Permission;
		$this->permissions = $perm->user_permissions($_USER['user_login'], 'poll');
		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions) &&
			!in_array('read', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}
	} // Poll

	function set_insert_rate($rate, $period)
	{
		$this->insert_period = $period;
		$this->insert_rate = $rate;
	} // set_insert_rate

	function load($poll_id = 0, $poll_pollid = 0, $poll_active = POLL_ACTIVE,
		$poll_date = '', $limit = 0)
	{
		global $db;

		if($poll_id)
			$sql = 'SELECT p.*, date_format(p.poll_entered, \''.$this->date_format.'\') poll_date FROM poll p WHERE poll_id = '.$poll_id;
		else
			$sql = 'SELECT p.*, date_format(p.poll_entered, \''.$this->date_format.'\') poll_date FROM poll p WHERE poll_pollid = '.$poll_pollid;

		if($poll_active)
			$sql .= ' AND poll_active = "'.$poll_active.'"';

		if($poll_date)
			$sql .= " AND poll_entered <= '$poll_date'";

		if($poll_pollid)
			$sql .= ' ORDER BY poll_entered';
		else
			$sql .= ' ORDER BY poll_entered DESC';

		$limit = (integer)$limit;
		if($limit)
			$sql .= ' LIMIT 0,'.$limit;

		if($poll_id) {
			return $db->ExecuteSingle($sql);
		} else
			return $db->Execute($sql);
	} // load

	function insert($poll_pollid, &$data, $validate = POLL_VALIDATE)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		if($validate)
			$this->validate($data);

		$this->error_msg = '';

		if(!$data['poll_name'])
			$this->error_msg .= 'Nav norādīts nosaukums<br>';

		if($this->error_msg)
			return false;

		$sql = "
		INSERT INTO poll (
			poll_pollid, poll_name, poll_active, poll_entered
		) VALUES (
			$poll_pollid, '$data[poll_name]', '$data[poll_active]', ".$db->now()."
		)";

		if($db->Execute($sql))
			return last_insert_id();
		else
			return false;
	} // insert

	function update($poll_id, &$data, $validate = POLL_VALIDATE)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		$poll_id = (integer)$poll_id;

		$this->error_msg = '';
		if(!$poll_id)
			$this->error_msg .= 'Nav norādīts vai nepareizs ID<br>';

		if(!$data['poll_name'])
			$this->error_msg .= 'Nav norādīts nosaukums<br>';

		if($this->error_msg)
			return false;

		if($validate)
			$this->validate($data);

		$sql = 'UPDATE poll SET ';
		$sql .= "poll_name = '$data[poll_name]', ";
		$sql .= "poll_active = '$data[poll_active]', ";
		$sql .= $data['poll_entered'] ? "poll_entered = '$data[poll_entered]', " : '';
		$sql = substr($sql, 0, -2);
		$sql .= ' WHERE poll_id = '.$poll_id;

		$db->Execute($sql);
		return $poll_id;
	} // update

	function del_under($poll_id)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		$poll_id = (integer)$poll_id;

		if(!$poll_id)
			return true;

		$ret = true;

		$sql = "SELECT poll_id FROM poll WHERE poll_pollid = ".$poll_id;
		$data = $db->Execute($sql);
		foreach($data as $item)
			$ret = $ret && $this->del($item['poll_id']);

		$sql = "DELETE FROM poll WHERE poll_pollid = ".$poll_id;

		return $ret && $db->Execute($sql);
	} // del_under

	function del($poll_id)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		$poll_id = (integer)$poll_id;

		if(!$poll_id)
			return true;

		$ret = $this->del_under($poll_id);
		$ret &= $this->del_votes($poll_id);

		$sql = 'DELETE FROM poll WHERE poll_id = '.$poll_id;

		return $ret && $db->Execute($sql);
	} // del

	function del_votes($poll_id, $poll_pollid = 0)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		$poll_id = (integer)$poll_id;
		$poll_pollid = (integer)$poll_pollid;

		$sql = 'DELETE FROM poll_votes WHERE pv_pollid = '.$poll_id;

		if($poll_pollid)
			$sql .= ' AND pv_poll_pollid = '.$poll_pollid;

		return $db->Execute($sql);
	} // del_votes

	function activate($poll_id)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		$poll_id = (integer)$poll_id;

		$sql = 'UPDATE poll SET poll_active = "'.POLL_ACTIVE.'" WHERE poll_id = '.$poll_id;

		return $db->Execute($sql);
	} // activate

	function deactivate($poll_id)
	{
		global $db;

		if(
			!in_array('admin', $this->permissions) &&
			!in_array('write', $this->permissions)
		) {
			$this->error_msg = ACCESS_DENIED;
			return false;
		}

		$poll_id = (integer)$poll_id;

		$sql = 'UPDATE poll SET poll_active = "'.POLL_INACTIVE.'" WHERE poll_id = '.$poll_id;

		return $db->Execute($sql);
	} // deactivate

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

		if(isset($data['item_count']) && $func)
			for($r = 1; $r <= $data['item_count']; ++$r)
				// ja iechekots, proceseejam
				if(isset($data['poll_checked'.$r]) && isset($data['poll_id'.$r]))
					$ret = $ret && $this->{$func}($data['poll_id'.$r]);

		return $ret;
	} // process_action

	function validate(&$data)
	{
		if(isset($data['poll_id']))
			$data['poll_id'] = !ereg('[0-9]', $data['poll_id']) ? 0 : $data['poll_id'];
		else
			$data['poll_id'] = 0;

		if(isset($data['poll_active']))
			$data['poll_active'] = ereg('[^YN]', $data['poll_active']) ? POLL_ACTIVE : $data['poll_active'];
		else
			$data['poll_active'] = POLL_ACTIVE;

		if(!isset($data['poll_name']))
			$data['poll_name'] = '';

		if(!isset($data['poll_entered']))
			$data['poll_entered'] = '';

	} // validate

	function count_votes($poll_id, $poll_pollid = 0)
	{
		global $db;

		$poll_id = (integer)$poll_id;
		$poll_pollid = (integer)$poll_pollid;

		$sql = 'SELECT COUNT(*) count_votes FROM poll_votes WHERE pv_pollid = '.$poll_id;

		if($poll_pollid)
			$sql .= ' AND pv_poll_pollid = '.$poll_pollid;

		$data = $db->ExecuteSingle($sql);
		return isset($data['count_votes']) ? $data['count_votes'] : 0;
	} // count_votes

	function show_results2(&$template, $id = 0)
	{
		$now = date('Y-m-d H:i:s');

		$template->set_file('FILE_poll_results2', 'tmpl.poll_results2.php');
		$template->copy_block('BLOCK_middle', 'FILE_poll_results2');

		$polls = $this->load(0, 0, POLL_ACTIVE, $now);
		if(!count($polls))
			return false;

		$poll = array();
		if(!$id)
			$poll = $polls[0];
		else
			foreach($polls as $p)
				if($p['poll_id'] == $id)
				{
					$poll = $p;
					break;
				}

		if(!$poll)
		{
			return;
		}

		$template->set_var('poll_question_b', $poll['poll_name'], 'BLOCK_middle');

		$total_votes = $this->count_votes($poll['poll_id']);
		$template->set_var('total_votes_b', $total_votes, 'BLOCK_middle');

		// atbildes
		$data = $this->load(0, $poll['poll_id']);
		if(count($data))
		{
			$template->enable('BLOCK_poll_r_items');
		}

		foreach($data as $item)
		{
			$item_votes = $this->count_votes($poll['poll_id'], $item['poll_id']);
			$template->set_var('poll_name_b', $item['poll_name'], 'BLOCK_middle');
			$template->set_var('count_votes_b', $item_votes, 'BLOCK_middle');

			if(!$item_votes)
				$template->disable('BLOCK_poll_r_bar');
			else
				$template->enable('BLOCK_poll_r_bar');

			if($total_votes)
			{
				$koef = ($item_votes / $total_votes);
				$template->set_var('poll_width', (int)($koef * 120), 'BLOCK_middle');
				$template->set_var('count_percent_b', number_format($koef * 100, 0), 'BLOCK_middle');
			} else
				$template->set_var('count_percent_b', 0, 'BLOCK_middle');

			$template->parse_block('BLOCK_poll_r_items', TMPL_APPEND);
		}

		if(count($polls) < 2)
		{
			return;
		}

		$template->enable('BLOCK_poll_archive');
		foreach($polls as $poll)
		{
			//if($id == $poll['poll_id'])
				//continue;
			$poll['poll_name'] = strip_tags($poll['poll_name']);
			$template->set_array_prefix($poll, '_a', 'BLOCK_middle');
			$template->parse_block('BLOCK_poll_archive_item', TMPL_APPEND);
		}

	} // show_results2

	function show_results(&$template)
	{
		$now = date('Y-m-d H:i:s');

		//$template->_copy_vars_byname('FILE_poll', 'FILE_index');
		$template->set_file('FILE_poll', 'tmpl.poll_results.php');
		$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_poll');
		//$template->copy_block('BLOCK_poll', 'FILE_poll');
		//$template->copy_block('BLOCK_right_item', 'FILE_poll', TMPL_APPEND);

		$data = $this->load(0, 0, POLL_ACTIVE, $now, 1);

		if(!count($data))
		{
			//$template->disable('FILE_poll');
			return false;
		}

		$poll = $data[0];
		$template->set_var('poll_question', $poll['poll_name'], 'FILE_poll');
		$template->set_var('poll_question_id', $poll['poll_id'], 'FILE_poll');

		$total_votes = $this->count_votes($poll['poll_id']);
		$template->set_var('total_votes', $total_votes, 'FILE_poll');

		// atbildes
		$data = $this->load(0, $poll['poll_id']);
		if(count($data))
			$template->enable('BLOCK_poll_items');

		foreach($data as $item)
		{
			$item_votes = $this->count_votes($poll['poll_id'], $item['poll_id']);
			$template->set_var('poll_name', $item['poll_name'], 'FILE_poll');
			$template->set_var('poll_id', $item['poll_id'], 'FILE_poll');
			$template->set_var('count_votes', $item_votes, 'FILE_poll');

			if(!$item_votes)
				$template->disable('BLOCK_poll_bar');
			else
				$template->enable('BLOCK_poll_bar');

			if($total_votes) {
				$koef = ($item_votes / $total_votes);
				$template->set_var('poll_width', (int)($koef * 120), 'FILE_poll');
				$template->set_var('count_percent', number_format($koef * 100, 0), 'FILE_poll');
			} else
				$template->set_var('count_percent', 0, 'FILE_poll');

			$template->parse_block('BLOCK_poll_items', TMPL_APPEND);
		}

		$template->parse_block('FILE_poll');
		$template->set_var('right_item_data', $template->get_parsed_content('FILE_poll'), 'BLOCK_right_item');
		$template->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // show_results

	function set_poll(&$template)
	{
		global $db;

		if( !(user_loged() && empty($GLOBALS['poll_results'])) )
			return $this->show_results($template);

		$now = date('Y-m-d H:i:s');

		if( !($data = $this->load(0, 0, POLL_ACTIVE, $now, 1)) )
			return false;

		$poll_data = $data[0];

		// ja jau nobalsots
		//if($GLOBALS['i_am_admin'])
		{
			$sql = sprintf(
				"SELECT COUNT(*) vote_count FROM poll_votes WHERE pv_userid = %d AND pv_pollid = %d",
				$_SESSION['login']['l_id'],
				$poll_data['poll_id']
			);
			$check_votes = $db->ExecuteSingle($sql);
			if($check_votes['vote_count'] > 0)
			{
				return $this->show_results($template);
			}
		}
		/*
		else {
			if(isset($_SESSION['poll']['votes']))
			{
				foreach($_SESSION['poll']['votes'] as $item)
				{
					if(
						isset($item['poll_id']) &&
						isset($item['poll_vote_date']) &&
						//($item['poll_vote_date'] == date('Ymd')) &&
						($item['poll_id'] == $data[0]['poll_id'])
					)
						return $this->show_results($template);
				}
			}
		}*/

		$template->set_file('FILE_poll', 'tmpl.poll.php');
		$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_poll');
		$template->enable('BLOCK_poll');

		$template->set_var('poll_question', $poll_data['poll_name'], 'FILE_poll');
		$template->set_var('poll_question_id', $poll_data['poll_id'], 'FILE_poll');

		// atbildes
		$data = $this->load(0, $poll_data['poll_id']);
		if(count($data))
			$template->enable('BLOCK_poll_items');

		foreach($data as $item)
		{
			$template->set_var('poll_name', $item['poll_name'], 'FILE_poll');
			$template->set_var('poll_id', $item['poll_id'], 'FILE_poll');
			$template->parse_block('BLOCK_poll_items', TMPL_APPEND);
		}

		$template->parse_block('FILE_poll');
		$template->set_var('right_item_data', $template->get_parsed_content('FILE_poll'), 'BLOCK_right_item');
		$template->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_poll

	function vote($poll_id, $poll_pollid)
	{
		global $db, $ip;

		$poll_id = (integer)$poll_id;
		$poll_pollid = (integer)$poll_pollid;
		$now = date('Y-m-d H:i:s');

		if(!$poll_id || !$poll_pollid || !user_loged())
			return false;

		$ban = new Ban;
		if($ban_info = $ban->banned($ip, 'poll'))
		{
			$this->error_msg = "Banned - ".$ban_info['ub_reason'];
			return false;
		}

		/*
		NOTE: Vairs nav aktuāls, jo balso tikai reģistrētie
		// paarbaudam vai nefloodo
		if($this->insert_rate && $this->insert_period)
		{
			$sql = "
			SELECT
				count(*) rate
			FROM
				poll_votes
			WHERE
				pv_entered >= DATE_SUB(NOW(), INTERVAL ".$this->insert_period." SECOND) AND 
				pv_userip='$ip'";

			$rate = $db->ExecuteSingle($sql);

			if($rate['rate'] >= $this->insert_rate) {
				$new_ban['ub_net'] = $ip;
				$new_ban['ub_moduleid'] = 'poll';
				$new_ban['ub_reason'] = 'Flood!';
				$ban->insert($new_ban);
				$this->error_msg = "Flood detected! Banned['$ip']";
				return false;
			}
		} // rate && period
		*/

		if( !($current_poll = $this->load(0, 0, POLL_ACTIVE, $now, 1)) )
			return false;

		$current_poll = $current_poll[0];
		if($current_poll['poll_id'] != $poll_id)
			return false;

		$sql = 'INSERT INTO poll_votes (pv_pollid, pv_poll_pollid, pv_userid, pv_userip, pv_entered) VALUES (';
		$sql .= $poll_id.', ';
		$sql .= $poll_pollid.', ';
		$sql .= $_SESSION['login']['l_id'].', ';
		$sql .= "'$ip', ";
		$sql .= $db->now();
		$sql .= ')';

		$ret = $db->Execute($sql);

		/*
		NOTE: Vairs nav aktuāls, jo balso tikai reģistrētie
		if($ret)
		{
			$new_vote['poll_id'] = $poll_id;
			$new_vote['poll_vote_date'] = date('Ymd');
			$_SESSION['poll']['votes'][] = $new_vote;
		}
		*/

		return $ret;
	} // vote

} // Poll

