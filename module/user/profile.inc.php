<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Logins.php');
require_once('lib/MainModule.php');

$login = array_shift($sys_parameters);

if($login)
{
	include("module/user/profile/user.inc.php");
} else {
# View and edit private profile
	include("module/user/profile/private.inc.php");
}

