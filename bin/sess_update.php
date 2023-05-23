<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');

ini_set('session.use_cookies', false);

session_start();

$res = DB::withNewTrans(function(){
	$allowed = ['forums', 'res'];
	$p = DB::Prepare("UPDATE logins SET l_sess_id = ?, l_sessiondata = ? WHERE l_id = ?");
	$q = DB::Query("SELECT * FROM sessions ORDER BY sess_lastaccess DESC");
	$sess_ids = [];
	while($data = DB::Fetch($q))
	{
		session_decode($data['sess_data']);

		// Ja nav l_id vai jau l_id agrāk redzēts, tad skip
		$l_id = $_SESSION['login']['l_id'] ?? null;
		if(!$l_id || isset($sess_ids[$l_id]))
		{
			continue;
		}

		$sess_ids[$l_id] = true;

		foreach($_SESSION as $k=>$_v){
			if(!in_array($k, $allowed)){
				unset($_SESSION[$k]);
			}
		}

		DB::ExecutePrepared($p, $data['sess_id'], session_encode(), $l_id);
	}

	return true;
});

if($res){
	print "Sessions updated\n";
} else {
	print "FAIL\n";
}
