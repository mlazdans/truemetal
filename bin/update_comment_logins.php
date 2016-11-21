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

$sql = "
SELECT
	c_username,
	(SELECT l_id FROM logins WHERE BINARY l_nick = c_username) AS l_id,
	(SELECT l_login FROM logins WHERE BINARY l_nick = c_username) AS l_login
FROM
	`comment`
WHERE
	`login_id` IS NULL
GROUP BY
	c_username
HAVING
	l_id IS NOT NULL
";

$items = $db->Execute($sql);

foreach($items as $item)
{
	$c_username = $db->Quote($item['c_username']);
	$l_login =  $db->Quote($item['l_login']);
	$sql = "
	UPDATE comment SET
		login_id = $item[l_id],
		c_userlogin = '$l_login'
	WHERE
		login_id IS NULL AND
		c_username = '$c_username'
	";
	$db->Execute($sql);
	$db->Execute("CALL logins_update_meta($item[l_id]);");
}

