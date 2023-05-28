<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');

ini_set('session.use_cookies', false);

register_shutdown_function(function(){
	while(ob_get_level()){
		print ob_get_clean();
	}
});

session_start();

$sess_ids = DB::withNewTrans(function(){
	ob_start();
	$allowed = ['forums', 'res'];
	$p = DB::Prepare("UPDATE logins SET l_sess_id = ?, l_sessiondata = ? WHERE l_id = ?");
	// $q = DB::Query("SELECT * FROM sessions ORDER BY sess_lastaccess DESC");
	$q = DB::Query("SELECT * FROM logins");
	$sess_ids = [];
	while($data = DB::Fetch($q))
	{
		if(empty($data['l_sessiondata'])){
			continue;
		}

		if(!session_decode($data['l_sessiondata'])){
			print_r($data);
			session_start();
			continue;
			// return false;
		}

		$l_id = $data['l_id'];

		// Ja nav l_id vai jau l_id agrāk redzēts, tad skip
		// $l_id = $_SESSION['login']['l_id'] ?? null;
		// if(!$l_id || isset($sess_ids[$l_id]))
		// {
		// 	continue;
		// }

		$sess_ids[$l_id] = true;

		foreach($_SESSION as $k=>$_v){
			if(!in_array($k, $allowed)){
				unset($_SESSION[$k]);
			}
		}

		if(!DB::ExecutePrepared($p, $data['l_sess_id'], session_encode(), $l_id)){
			return false;
		}
	}

	return $sess_ids;
});

if($sess_ids){
	printf("%d sessions updated\n", count($sess_ids));
} else {
	print "FAIL\n";
}
