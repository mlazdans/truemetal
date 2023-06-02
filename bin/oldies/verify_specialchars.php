<?php declare(strict_types = 1);

require_once('../../include/boot.php');
require_once('include/dbconnect.php');
require_once('include/console.php');

printf("SET NAMES utf8;\n");
printf("START TRANSACTION;\n");

function check_item(object $c, string $k): bool {
	if($c->$k){
		$check = htmlspecialchars_decode($c->$k);
		if($check != $c->$k){
			printf("-- old_res_name = %s\n", $c->$k);
			printf("-- new_res_name = %s\n", $check);
			printf("UPDATE res SET $k = '%s' WHERE res_id = %d;\n\n", DB::Quote($check), $c->res_id);
			return true;
		}
	}

	return false;
}

// res_name, res_email, res_nickname
function check(object $c){
	check_item($c, 'res_name') | check_item($c, 'res_email') | check_item($c, 'res_nickname');
}

$q = DB::Query("SELECT * FROM res WHERE res_name IS NOT NULL OR res_email IS NOT NULL OR res_nickname IS NOT NULL");
while($c = DB::FetchObject($q)){
	check($c);
}

printf("COMMIT;\n");
