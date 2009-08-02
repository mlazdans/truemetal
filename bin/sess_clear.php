<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

$i_am_admin = true;
require_once('../includes/inc.config.php');
require_once('../includes/inc.dbconnect.php');
require_once('lib/utils.php');
include_once('../includes/inc.console.php');

$deleted = $errors = 0;

$sql = "SELECT sess_ip, count(sess_ip) skaits FROM sessions GROUP BY sess_ip HAVING skaits > 100 ORDER BY skaits DESC";
$data = $db->Execute($sql);

foreach($data as $item)
{
	if($db->Execute("DELETE FROM sessions WHERE sess_ip = '$item[sess_ip]'"))
		++$deleted;
	else
		++$errors;
}


$sql = "
DELETE
FROM `sessions`
WHERE
sess_data = ''
";

$db->Execute($sql);
$deleted += mysql_affected_rows();

$period = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - 180, date('Y')));
$sql = "
DELETE
FROM `sessions`
WHERE
sess_lastaccess < '$period'
";
#TO_DAYS(NOW()) - TO_DAYS(sess_lastaccess) > 180

$db->Execute($sql);
$deleted += mysql_affected_rows();

$db->Execute("OPTIMIZE TABLE sessions");

print "Deleted:\t$deleted\n";

