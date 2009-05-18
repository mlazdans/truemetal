<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../includes/inc.config.php');
require_once('../classes/class.SQLLayer.php');

/* sleedzamies pie datubaazes */
if($sys_database_type) {
	$db = new SQLLayer($sys_database_type);
	$db->connect($sys_db_host, $sys_db_user, $sys_db_password, $sys_db_name, $sys_db_port);
}

if($sys_use_chache)
{
	$_CACHE = new Memcache;
	$_CACHE->connect($memcache_host, $memcache_port) or die ("Could not connect to memcache");
}
