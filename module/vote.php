<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// ----------------------------------------------------------------------------

# TODO: pārlikt visu šo zem lib

$value = array_shift($sys_parameters);
$res_id = (int)array_shift($sys_parameters);
$json = array_shift($sys_parameters) == 'json';
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

$Res = new Res();
$Res->setDb($db);
$res_data = $Res->Get(array(
	'res_id'=>$res_id,
	));

$cv_login_id = $res_data['login_id'];

if(!$res_data || ($cv_login_id == $login_id))
{
	if($json)
	{
		if($cv_login_id == $login_id)
			$retJson->msg = ($value == 'up' ? ":)" : ">:)");
		else
			$retJson->msg = "Comment not found";
		print json_encode($retJson);
	} else {
		if(empty($_SERVER['HTTP_REFERER']))
		{
			header("Location: $sys_http_root/");
		} else {
			header("Location: $_SERVER[HTTP_REFERER]");
		}
	}
	return;
}

# Check count
$date = date('Y-m-d H:i:s', time() - 24 * 3600); // 24h
$check_sql = sprintf(
	"SELECT COUNT(*) cv_count FROM `res_vote` WHERE login_id = %d AND rv_entered >= '%s'",
	$login_id,
	$date
);
$countCheck = $db->ExecuteSingle($check_sql);
if($countCheck['cv_count'] >= 24)
{
	if($json)
	{
		$retJson->msg = "Pārsniegtiņš 24 stundiņu limitiņš balsošaniņai.";
		print json_encode($retJson);
	} else {
		if(empty($_SERVER['HTTP_REFERER']))
		{
			header("Location: $sys_http_root/");
		} else {
			header("Location: $_SERVER[HTTP_REFERER]");
		}
	}
	return;
}

# Insert
$insert_sql = sprintf(
	"INSERT IGNORE INTO res_vote (res_id, login_id, rv_value, rv_userip, rv_entered) VALUES (%d, %d, %d, '%s', NOW())",
	$res_id,
	$login_id,
	$value == 'up' ? 1 : -1,
	$ip
);

if($db->Execute($insert_sql))
{
}

if($json)
{
	if($new_data = $Res->Get(array(
		'res_id'=>$res_id,
		)))
	{
		$retJson->Votes = $new_data['res_votes'];
	} else {
		$retJson->msg = "Comment not found";
	}

	print json_encode($retJson);
} else {
	if(empty($_SERVER['HTTP_REFERER']))
	{
		header("Location: $sys_http_root/");
	} else {
		header("Location: $_SERVER[HTTP_REFERER]");
	}
}


