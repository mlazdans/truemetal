<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// ----------------------------------------------------------------------------

# TODO: pārlikt uz class
require_once('lib/CommentConnect.php');

$value = array_shift($sys_parameters);
$c_id = (int)array_shift($sys_parameters);
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

$user_id = $_SESSION['login']['l_id'];

$Comment = new Comment;
$Comment->setDb($db);
$comment_data = $Comment->get(array(
	'c_id' => $c_id,
	));

if($comment_data)
	$cv_user_id = $comment_data['c_userid'];

if(!$comment_data || ($cv_user_id == $user_id))
{
	if($json)
	{
		if($cv_user_id == $user_id)
			$retJson->msg = ($value == 'up' ? ":)" : ">:)");
		else
			$retJson->msg = "Comment not found";
		print json_encode($retJson);
		return;
	}
	header("Location: $sys_http_root/");
	return;
}


$insert_sql = sprintf(
	"INSERT IGNORE INTO comment_votes (cv_c_id, cv_userid, cv_value, cv_userip, cv_entered) VALUES (%d, %d, %d, '%s', NOW())",
	$c_id,
	$user_id,
	$value == 'up' ? 1 : -1,
	$ip
);


if($db->Execute($insert_sql))
{
}

if($json)
{
	if($new_comment_data = $Comment->get(array(
		'c_id' => $c_id,
		)))
	{
		$retJson->Votes = $new_comment_data['c_votes'];
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

