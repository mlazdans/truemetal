<?php declare(strict_types = 1);

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
