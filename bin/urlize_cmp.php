<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/console.php');
require_once('include/dbconnect.php');

$diff_count = 0;
$q = DB::Query("SELECT res_id, res_name, urlize(res_name) urlized FROM res WHERE res_name IS NOT NULL");
while($r = DB::FetchObject($q)){
	$urlized = urlize($r->res_name);
	if($r->urlized != $urlized){
		$diff_count++;
		dumpr($r->res_id, $r->res_name, $r->urlized, $urlized);
	}
}

print "Diff count: $diff_count\n";
