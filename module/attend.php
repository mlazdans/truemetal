<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// ----------------------------------------------------------------------------

# TODO: pārlikt visu šo zem lib

$res_id = (int)array_shift($sys_parameters);
$off = array_shift($sys_parameters);
$retJson = new StdClass;

if(!user_loged())
{
	if($json)
	{
		$retJson->msg = "Access denied";
		print json_encode($retJson);
		return;
	}
	header("Location: $sys_http_root/");
	return;
}

$login_id = $_SESSION['login']['l_id'];

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
	print json_encode($retJson);
}

