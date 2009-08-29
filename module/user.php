<?php

$section = array_shift($sys_parameters);

if($section == 'image')
{
	include("module/user/image.inc.php");
}

if($section == 'thumb')
{
	include("module/user/thumb.inc.php");
}

