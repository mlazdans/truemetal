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

$db->AutoCommit(false);

$sql = "SELECT * FROM `gallery_group_old`";
$items = $db->Execute($sql);

foreach($items as $item)
{
}

$sql = "SELECT * FROM `gallery_old`";
$items = $db->Execute($sql);

foreach($items as $item)
{
}
$db->Commit();

