<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

// NOTE: vajadzētu disablot res triggerus - lai ātrāk
$res = DB::withNewTrans(function(){
	$q = DB::Query("SELECT * FROM res_merge WHERE ignored = 0");
	while($r = DB::FetchObject($q))
	{
		if(transform_comment_into_theme($r->forum_res_id, $r->comment_res_id)){
			println("Merged: $r->forum_res_id, $r->comment_res_id");
		} else {
			return false;
		}
	}

	return true;
});

if($res){
	println("Merged");
} else {
	println("FAIL");
}
