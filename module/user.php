<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$section = array_shift($sys_parameters);
$sections = [
	'image',
	'thumb',
	'profile',
	'viewimage',
	'comments',
	'pwch',
	'emailch',
];

if(($k = array_search($section, $sections)) !== FALSE){
	include('module/user/'.$sections[$k].'.inc.php');
} else {
	redirect("/");
}
