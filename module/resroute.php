<?php declare(strict_types = 1);

$location = "/";
$res_id = (int)array_shift($sys_parameters);

if($res_id){
	$location = Res::Route($res_id, (int)get('c_id'));
}

header("Location: $location");
