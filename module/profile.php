<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$section = array_shift($sys_parameters);
if($section == 'user')
{
	$user = array_pop($sys_parameters);
	header("Location: /user/profile/$user/", true, 301);
}

if($section == 'view')
{
	header($_SERVER["SERVER_PROTOCOL"]." 410 Removed from public eyes");
}

