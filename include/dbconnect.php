<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/SQLLayer.php');

/* sleedzamies pie datubaazes */
if($sys_database_type)
{
	$db = new SQLLayer($sys_database_type);
	$db->connect($sys_db_host, $sys_db_user, $sys_db_password, $sys_db_name, $sys_db_port);
}

