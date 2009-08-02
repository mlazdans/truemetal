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

