<?php declare(strict_types = 1);

use dqdp\DBA\driver\MySQL_PDO;
use dqdp\DBA\Types\MySQLConnectParams;

$params = new MySQLConnectParams(database: $sys_db_name, username: $sys_db_user, password: $sys_db_password, charset: 'utf8mb4');
$D = (new MySQL_PDO($params))->connect();
if(!$D->get_conn()){
	die('True DB error!');
}

DB::set_db($D);
