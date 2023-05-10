<?php declare(strict_types = 1);

$db = new SQLLayer('utf8mb4');
$db->connect(
	$GLOBALS['sys_db_host'],
	$GLOBALS['sys_db_user'],
	$GLOBALS['sys_db_password'],
	$GLOBALS['sys_db_name'], $GLOBALS['sys_db_port']
);

if(!$db->conn){
	die('True DB error!');
}
