<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

define('POLL_ACTIVE', 'Y');
define('POLL_INACTIVE', 'N');
define('POLL_ALL', false);
define('POLL_VALIDATE', true);
define('POLL_DONTVALIDATE', false);

class Poll
{
	var $date_format;
	var $error_msg;

	function __construct(){
		$this->date_format = '%d.%m.%Y %H:%i';
	} // __construct

	function load($poll_id = 0, $poll_pollid = 0, $poll_active = POLL_ACTIVE, $poll_date = '', $limit = 0)
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

		if($validate)
			$this->validate($data);

		$this->error_msg = '';

		if(!$data['poll_name'])
			$this->error_msg .= 'Nav norādīts nosaukums<br />';

		if($this->error_msg)
			return false;

		$sql = "
		INSERT INTO poll (
			poll_pollid, poll_name, poll_active, poll_entered
		) VALUES (
			$poll_pollid, '$data[poll_name]', '$data[poll_active]', ".$db->now()."
		)";

		return ($db->Execute($sql) ? $db->LastID() : false);
	} // insert

	function update($poll_id, &$data, $validate = POLL_VALIDATE)
	{
		global $db;

		$poll_id = (integer)$poll_id;

		$this->error_msg = '';
		if(!$poll_id)
			$this->error_msg .= 'Nav norādīts vai nepareizs ID<br />';

		if(!$data['poll_name'])
			$this->error_msg .= 'Nav norādīts nosaukums<br />';

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

		$poll_id = (integer)$poll_id;

		$sql = 'UPDATE poll SET poll_active = "'.POLL_ACTIVE.'" WHERE poll_id = '.$poll_id;

		return $db->Execute($sql);
	} // activate

	function deactivate($poll_id)
	{
		global $db;

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
				# ja iechekots, proceseejam
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

	protected function __set_poll(&$template, $poll, $data, $block)
	{
		$total_votes = $this->count_votes($poll['poll_id']);
		foreach($data as $c=>$item)
		{
			$item_votes = $this->count_votes($poll['poll_id'], $item['poll_id']);
			$template->set_var('poll_name', $item['poll_name'], $block);
			$template->set_var('poll_id', $item['poll_id'], $block);
			$template->set_var('poll_row', $c % 2, $block);
			$template->set_var('count_votes', $item_votes, $block);


			if($total_votes) {
				$koef = ($item_votes / $total_votes);
				$template->set_var('poll_width', (int)($koef * 120), $block);
				$template->set_var('count_percent', number_format($koef * 100, 0), $block);
			} else
				$template->set_var('count_percent', 0, $block);

			$template->parse_block($block, TMPL_APPEND);
		}
	} // __set_poll

	function show_archive(&$template, $id = 0)
	{
		$now = date('Y-m-d H:i:s');

		$template->set_file('FILE_poll_archive', 'poll/archive.tpl');
		$template->copy_block('BLOCK_middle', 'FILE_poll_archive');

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

		$template->set_var('poll_question', $poll['poll_name'], 'BLOCK_middle');

		$total_votes = $this->count_votes($poll['poll_id']);
		$template->set_var('total_votes', $total_votes, 'BLOCK_middle');
		$template->set_title(ent("Balsošana: $poll[poll_name] rezultāti"));

		# atbildes
		$data = $this->load(0, $poll['poll_id']);
		if(count($data)){
			$template->enable('BLOCK_poll_r_items');
			$this->__set_poll($template, $poll, $data, 'BLOCK_poll_r_items');
		}


		if(count($polls) < 2){
			return;
		}

		$template->enable('BLOCK_poll_archive');
		foreach($polls as $poll){
			$poll['poll_name'] = strip_tags($poll['poll_name']);
			$template->set_array_prefix($poll, '_a', 'BLOCK_middle');
			$template->parse_block('BLOCK_poll_archive_item', TMPL_APPEND);
		}

	} // show_archive

	function show_results(&$template)
	{
		$now = date('Y-m-d H:i:s');

		$template->set_file('FILE_poll', 'poll/results.tpl');
		$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_poll');

		$data = $this->load(0, 0, POLL_ACTIVE, $now, 1);

		if(!count($data)){
			return false;
		}

		$poll = $data[0];
		$template->set_var('poll_question', $poll['poll_name'], 'FILE_poll');
		$template->set_var('poll_question_id', $poll['poll_id'], 'FILE_poll');

		$total_votes = $this->count_votes($poll['poll_id']);
		$template->set_var('total_votes', $total_votes, 'FILE_poll');

		# atbildes
		$data = $this->load(0, $poll['poll_id']);
		if(count($data))
		{
			$template->enable('BLOCK_poll_items');
			$this->__set_poll($template, $poll, $data, 'BLOCK_poll_items');
		}

		$template->parse_block('FILE_poll');
		$template->set_var('right_item_data', $template->get_parsed_content('FILE_poll'), 'BLOCK_right_item');
		$template->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // show_results

	function set_poll(&$template)
	{
		global $db;

		if(!user_loged())
			return $this->show_results($template);

		$now = date('Y-m-d H:i:s');

		if( !($data = $this->load(0, 0, POLL_ACTIVE, $now, 1)) )
			return false;

		$poll_data = $data[0];

		# ja jau nobalsots
		$sql = sprintf(
			"SELECT COUNT(*) vote_count FROM poll_votes WHERE pv_userid = %d AND pv_pollid = %d",
			$_SESSION['login']['l_id'],
			$poll_data['poll_id']
		);
		$check_votes = $db->ExecuteSingle($sql);
		if($check_votes['vote_count'] > 0){
			return $this->show_results($template);
		}

		$template->set_file('FILE_poll', 'poll.tpl');
		$template->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_poll');
		$template->enable('BLOCK_poll');

		$template->set_var('poll_question', $poll_data['poll_name'], 'FILE_poll');
		$template->set_var('poll_question_id', $poll_data['poll_id'], 'FILE_poll');

		# atbildes
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

		return $ret;
	} // vote
} // Poll

