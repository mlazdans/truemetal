<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

function check_item(object $c, string $k): bool {
	if($c->$k){
		$check = htmlspecialchars_decode($c->$k);
		if($check != $c->$k){
			printf("UPDATE res SET $k = '%s' WHERE res_id = %d;\n", DB::Quote($check), $c->res_id);
			return true;
		}
	}

	return false;
}

// res_name, res_email, res_nickname
function check(object $c){
	check_item($c, 'res_name') | check_item($c, 'res_email') | check_item($c, 'res_nickname');
}

$q = DB::Query("SELECT * FROM res WHERE table_id=3");
while($c = DB::FetchObject($q)){
	check($c);
}
