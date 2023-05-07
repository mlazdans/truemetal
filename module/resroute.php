<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$location = "/";
$res_id = (int)array_shift($sys_parameters);

if($res_id){
	$location = Res::Route($res_id, (int)get('c_id'));
}

header("Location: $location");

