<?php declare(strict_types = 1);

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
