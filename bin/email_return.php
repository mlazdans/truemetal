#!/usr/local/php7/bin/php
<?php

/*
$KERNEL_LEAVE_AFTER_INIT = true;
require_once(dirname(__FILE__).'/../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');
*/

$sys_root              = realpath(dirname(__FILE__).'/../');

$data= '';
$fp = fopen("php://stdin", "r");
while (! feof($fp)) {
	$data.= fgets($fp);
}
fclose($fp);

/*
ob_start();
print_r($argv);
print_r($_SERVER);
print_r($_ENV);
$data = ob_get_clean();
*/

file_put_contents("$sys_root/tmp/return.log", $data."\n\n", FILE_APPEND);

exit(0);

