<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

ini_set('session.use_cookies', false);

register_shutdown_function(function(){
	while(ob_get_level()){
		print ob_get_clean();
	}
});

session_start();

function mark_as_seen(int $res_id, mixed $seen_date): bool {
	if($seen_date && is_string($seen_date))
	{
		if(($ts = strtotime($seen_date)) !== false){
			$_SESSION['res_seen_ts'][$res_id] = $ts;
			return true;
		}
	}

	return false;
}

printf("SET NAMES utf8;\n");
printf("START TRANSACTION;\n");

$sess_ids = (function(){
	ob_start();
	$allowed = ['forums', 'res'];

	$logins = (new LoginsEntity)->getAll(new LoginsFilter(l_active:false, l_accepted:false));
	foreach($logins as $L)
	{
		if(empty($L->l_sessiondata)){
			continue;
		}

		if(!session_decode($L->l_sessiondata)){
			print_r($L);
			session_start();
			continue;
		}

		$sess_ids[$L->l_id] = true;

		foreach($_SESSION as $k=>$_v){
			if(!in_array($k, $allowed)){
				unset($_SESSION[$k]);
			}
		}

		if(isset($_SESSION['res']['viewed_before'])){
			$_SESSION['res_marked_seen_ts'] = $_SESSION['res']['viewed_before'];
		}

		if(isset($_SESSION['res']['viewed_date'])){
			if(is_array($_SESSION['res']['viewed_date'])){
				$_SESSION['res_seen_ts'] = [];
				foreach($_SESSION['res']['viewed_date'] as $res_id=>$seen_date){
					mark_as_seen($res_id, $seen_date);
				}
			}
		}

		if(isset($_SESSION['forums']['viewed_date'])){
			if(is_array($_SESSION['forums']['viewed_date'])){
				foreach($_SESSION['forums']['viewed_date'] as $forum_id=>$seen_date){
					if($F = ViewResForumEntity::getById($forum_id, false, new ResForumFilter(forum_allow_childs:1))){
						// print "-- Merged: forum_id:$forum_id to res_id:$F->res_id\n";
						mark_as_seen($F->res_id, $seen_date);
					} else {
						print "-- WARNING: res not found for forum_id:$forum_id\n";
					}
				}
			}
			unset($_SESSION['forums']);
		}

		unset($_SESSION['forums']['viewed_before']);
		unset($_SESSION['forums']['viewed']);
		unset($_SESSION['res']);

		if(!empty($_SESSION['forums'])){
			print "-- ERROR: _SESSION['forums'] not empty\n";
			print_r($_SESSION);
			exit(1);
		}

		unset($_SESSION['forums']);

		$sess_data = ($s = session_encode()) ? "'".DB::Quote($s)."'" : 'NULL';
		printf("UPDATE logins SET l_sessiondata = %s WHERE l_id = $L->l_id;\n", $sess_data);
	}

	return $sess_ids??[];
})();

printf("COMMIT;\n");

if($sess_ids){
	fprintf(STDERR, "%d sessions updated\n", count($sess_ids));
} else {
	fprintf(STDERR, "FAIL\n");
}
