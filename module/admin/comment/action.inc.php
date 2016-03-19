<?php

require_once('lib//Comment.php');

$c_ids = post('c_id');
if(!is_array($c_ids))
	$c_ids = array($c_id);

$ok = true;
$func = substr($action, 8);

$db->AutoCommit(false);

$Comment = new Comment;
$Comment->setDb($db);
foreach($c_ids as $c_id)
	$ok = $Comment->{$func}($c_id) ? $ok : false;

if($ok)
	$db->Commit();

return $ok;


