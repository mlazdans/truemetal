<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

$ret = DB::Query("DROP TRIGGER IF EXISTS res_trigger1") && DB::Query("DROP TRIGGER IF EXISTS res_vote_trigger3");

if(!$ret){
	print "Drop triggers failed\n";
	exit(1);
}

$res = DB::withNewTrans(function(){
	$q = DB::Query("SELECT * FROM res_merge WHERE ignored = 0");
	while($r = DB::FetchObject($q))
	{
		if(transform_comment_into_theme($r->forum_res_id, $r->comment_res_id)){
			print "Merged: $r->forum_res_id, $r->comment_res_id\n";
		} else {
			return false;
		}
	}

	return true;
});

if($res){
	print "Merged\n";
} else {
	print "FAIL\n";
}

print "\nTODO:\n";
print "recreate triggers: TRIG_res_trigger1_AFTER_UPDATE.sql, TRIG_res_vote_trigger3_AFTER_UPDATE.sql\n";
print "CALL res_update_meta(null);\n";
print "CALL logins_update_meta(null);\n";
