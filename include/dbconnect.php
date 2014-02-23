<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/SQLLayer.php');

/* sleedzamies pie datubaazes */
if(!empty($GLOBALS['sys_database_type']))
{
	$db = new SQLLayer($GLOBALS['sys_database_type']);
	$db->connect(
		$GLOBALS['sys_db_host'],
		$GLOBALS['sys_db_user'],
		$GLOBALS['sys_db_password'],
		$GLOBALS['sys_db_name'], $GLOBALS['sys_db_port']
		);
} else {
	$db = null;
	$db->AutoCommit(true);
}

