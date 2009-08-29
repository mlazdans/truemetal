<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$section = array_shift($sys_parameters);

if($section == 'image')
{
	include("module/user/image.inc.php");
}

if($section == 'thumb')
{
	include("module/user/thumb.inc.php");
}

if($section == 'profile')
{
	include("module/user/profile.inc.php");
}

if($section == 'viewimage')
{
	include("module/user/viewimage.inc.php");
}

