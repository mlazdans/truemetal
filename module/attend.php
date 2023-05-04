<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# TODO: pārlikt visu šo zem lib
require_once('lib/Res.php');

$res_id = (int)array_shift($sys_parameters);
$off = array_shift($sys_parameters);
$json = isset($_GET['json']);

$retJson = new StdClass;
$redirect = "/resroute/$res_id/";

if(!user_loged())
{
	$retJson->msg = "Access denied";
	if($json)
	{
		print json_encode($retJson);
	} else {
		print $retJson->msg;
	}
	return;
}

$login_id = $_SESSION['login']['l_id'];

$Res = new Res();
$item = $Res->GetAllData($res_id);

if(!isset($item['type_id']) || ($item['type_id'] != Res::TYPE_EVENT)){
	$retJson->msg = "Access denied";
	if($json){
		print json_encode($retJson);
	} else {
		print $retJson->msg;
	}
	return;
}

if(time() > (strtotime(date('d.m.Y', strtotime($item['event_startdate']))) + 24 * 3600)){
	$retJson->msg = "Par vēlu";
	if($json){
		print json_encode($retJson);
	} else {
		print $retJson->msg;
	}
	return;
}

if($off == 'off'){
	$sql = sprintf(
		"UPDATE attend SET a_attended = 0 WHERE l_id = %d AND res_id = %d",
		$login_id,
		$res_id
		);
} else {
	$sql = sprintf(
		"INSERT INTO attend (l_id, res_id) VALUES (%d, %d) ON DUPLICATE KEY UPDATE a_attended = 1",
		$login_id,
		$res_id
		);
}

if($db->Execute($sql))
{
	/*
	$sql = sprintf("
		SELECT
			a.*, l.l_nick
		FROM
			attend a
		JOIN logins l ON l.l_id = a.l_id
		WHERE
			res_id = %d
		ORDER BY
			a_entered
		",
		$res_id
		);

	$retJson->attendees = $db->Execute($sql);
	*/
	if($json){
		print json_encode($retJson);
	} else {
		header('Location: '.$redirect);
	}
}

