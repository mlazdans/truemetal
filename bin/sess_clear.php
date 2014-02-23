<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

$deleted = $errors = 0;

$sql = "SELECT `sess_ip`, COUNT(`sess_ip`) skaits FROM sessions GROUP BY `sess_ip` HAVING skaits > 50 ORDER BY skaits DESC";
$data = $db->Execute($sql);

foreach($data as $item)
{
	if($db->Execute("DELETE FROM `sessions` WHERE `sess_ip` = '$item[sess_ip]'"))
		++$deleted;
	else
		++$errors;
}

$sql = "DELETE FROM `sessions` WHERE sess_data = '' OR sess_data = 'login|a:0:{}'";
$db->Execute($sql);
$deleted += $db->AffectedRows();

$period = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - 180, date('Y')));
$sql = "DELETE FROM `sessions` WHERE `sess_lastaccess` < '$period'";

$db->Execute($sql);
$deleted += $db->AffectedRows();

$db->Execute("OPTIMIZE TABLE sessions");

print "Deleted:\t$deleted\n";

