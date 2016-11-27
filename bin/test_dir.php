<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

$abs_path = "$sys_root/public/cache/1450942600-8090image.jpg";
$dir = dirname($abs_path);

$key = ftok($dir, "T");
$se = sem_get($key);
sem_acquire($se);

print "AAA\n";

sem_release($se);

