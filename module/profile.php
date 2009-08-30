<?php

//printr($sys_parameters);
$section = array_shift($sys_parameters);
if($section == 'user')
{
	$user = array_pop($sys_parameters);
	header("Location: $sys_http_root/user/profile/$user/", true, 301);
	return;
}

