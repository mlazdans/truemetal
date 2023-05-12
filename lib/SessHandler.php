<?php declare(strict_types = 1);

class SessHandler
{
	var $ip;
	var $sess_name;
	var $timeout;
	var $max_time_online;
	var $sess_period;

	function __construct()
	{
		$this->ip = $GLOBALS["ip"];
		$this->timeout = 0;
		$this->sess_name = '';
		$this->max_time_online = 300;
	}

	function get_sess_name()
	{
		return (isset($this->sess_name) ? $this->sess_name : '');
	}

	function get_sess($sess_id)
	{
		$sql = "SELECT s.*, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(s.sess_lastaccess)) sess_period FROM sessions s WHERE sess_id = ?";
		$sess = DB::ExecuteSingle($sql, $sess_id);

		# if session exists
		if(isset($sess['sess_id']) && ($sess['sess_id'] == $sess_id)) {
			$sql = "UPDATE sessions SET sess_lastaccess = NOW() WHERE sess_id = ?";
			DB::Execute($sql, $sess_id);
		}

		if(isset($sess['sess_period']))
			$this->sess_period = $sess['sess_period'];

		return $sess;
	}

	function sess_open($save_path, $sess_name)
	{
		$this->sess_name = $sess_name;

		return true;
	}

	function sess_close()
	{
		return true;
	}

	function sess_write($sess_id, $sess_data)
	{
		if(empty($sess_data) || !user_loged())
			return true;

		$sess = $this->get_sess($sess_id);

		if(isset($sess['sess_id']) && ($sess['sess_id'] == $sess_id))
			$sql = "UPDATE sessions SET sess_data = '$sess_data', sess_lastaccess = NOW() WHERE sess_id = '$sess_id'";
		else
			$sql = "INSERT INTO sessions (sess_id, sess_data, sess_ip, sess_lastaccess, sess_entered) VALUES ('$sess_id', '$sess_data', '$this->ip', NOW(), NOW())";

		DB::Execute($sql);
		// $this->db->Commit();

		return true;
	}

	function sess_read($sess_id)
	{
		$sess = $this->get_sess($sess_id);

		if(!count($sess))
			return "";

		if($this->timeout && ($sess['sess_period'] > $this->timeout)) {
			$this->sess_destroy($sess_id);
			return "";
		}

		# if session exists
		if($sess['sess_id'] == $sess_id)
			return($sess['sess_data']);
		else
			return "";
	}

	function sess_destroy($sess_id)
	{
		unset($this->sess_name);
		unset($_SESSION);

		return DB::Execute("DELETE FROM sessions WHERE sess_id = ?", $sess_id);
	}

	function set_timeout($timeout)
	{
		$this->timeout = $timeout;
	}

	function sess_gc($maxlifetime)
	{
		//$period = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - 180, date('Y')));
		# 180 days: 24*3600*180
		$period = date('Y-m-d', time() - 15552000);

		$sql = array(
			"DELETE FROM `sessions` WHERE sess_data = '' OR sess_data = 'login|a:0:{}'",
			"DELETE FROM `sessions` WHERE `sess_lastaccess` < '$period'",
			"OPTIMIZE TABLE sessions",
			);

		foreach($sql as $q){
			DB::Execute($q);
		}

		return true;
	}

	function get_active()
	{
		$sql = "SELECT sess_data, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(sess_lastaccess)) sess_period, sess_ip
		FROM sessions
		WHERE sess_lastaccess > NOW() - ".$this->max_time_online."
		HAVING sess_period < ".$this->max_time_online;

		return DB::Execute($sql);
	}
}
