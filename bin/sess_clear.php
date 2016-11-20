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

$deleted = 0;

$CHUNK = 100000;

do
{
	$sql = "DELETE FROM `sessions` WHERE sess_data = '' OR sess_data = 'login|a:0:{}' LIMIT $CHUNK";
	$db->Execute($sql);
	$aff = $db->AffectedRows();
	print "Deleted $aff rows\n";
	$deleted += $aff;
	sleep(2);
} while ($aff==$CHUNK);

$period = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - 180, date('Y')));
do
{
	$sql = "DELETE FROM `sessions` WHERE `sess_lastaccess` < '$period' LIMIT $CHUNK";
	$db->Execute($sql);
	$aff = $db->AffectedRows();
	print "Deleted $aff rows\n";
	$deleted += $aff;
	sleep(2);
} while ($aff==$CHUNK);

$sql = "SELECT `sess_ip`, COUNT(`sess_ip`) skaits FROM sessions GROUP BY `sess_ip` HAVING skaits > 50 ORDER BY skaits DESC";
$data = $db->Execute($sql);

$ips = array();
foreach($data as $item){
	$ips[] = $item['sess_ip'];
}

while($ips)
{
	$i = array_splice($ips, 0, 50);
	$sql = "DELETE FROM `sessions` WHERE `sess_ip` IN ('".join("','", $i)."') LIMIT $CHUNK";
	do
	{
		$db->Execute($sql);
		$aff = $db->AffectedRows();
		print "Deleted $aff rows\n";
		$deleted += $aff;
		sleep(2);
	} while ($aff==$CHUNK);
}

$db->Execute("OPTIMIZE TABLE sessions");

print "Deleted:\t$deleted\n";

