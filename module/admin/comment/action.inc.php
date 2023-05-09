<?php declare(strict_types = 1);

$c_ids = post('c_id');
$new_res_id = post('new_res_id');

if(!is_array($c_ids))
	$c_ids = array($c_id);

$ok = true;
$func = substr($action, 8);

$db->AutoCommit(false);

if($func == 'move'){
	$RC = new ResComment;
	$RC->setDb($db);
	foreach($c_ids as $c_id)
		$ok = $RC->{$func}($c_id, $new_res_id) ? $ok : false;

	if($ok)
		$db->Commit();
} else {
	$Comment = new Comment;
	$Comment->setDb($db);
	foreach($c_ids as $c_id)
		$ok = $Comment->{$func}($c_id) ? $ok : false;

	if($ok)
		$db->Commit();
}

return $ok;
