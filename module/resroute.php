<?php

require_once('lib/Res.php');
$res_id = (int)array_shift($sys_parameters);

$Res = new Res();
if(!($resource = $Res->GetAllData($res_id))){
	header("Location: /");
	return;
}

$c_id = get('c_id');

$location = "/";
switch($resource['table_id'])
{
	case Table::ARTICLE:
		$location = "/$resource[module_id]/$resource[art_id]-".urlize($resource['art_name']).($c_id ? "#comment$c_id" : "");
		break;
	case Table::FORUM:
		$location = "/forum/$resource[forum_id]-".urlize($resource['forum_name']).($c_id ? "#comment$c_id" : "");
		break;
	case Table::COMMENT:
		break;
}

//print $location;
header("Location: $location");


