<?php declare(strict_types = 1);

$res_ids = (array)post('res_ids');
$new_res_id = post('new_res_id');

$ok = true;
$func = substr($action, 8);

if($func == 'move'){
	$RC = new ResComment;
	$RC->setDb($db);
	foreach($c_ids as $c_id)
		$ok = $RC->{$func}($c_id, $new_res_id) ? $ok : false;

	if($ok)
		$db->Commit();

	return $ok;
} elseif($func == 'show'){
	return (new ResEntity)->show($res_ids);
} elseif($func == 'hide'){
	return (new ResEntity)->hide($res_ids);
} else {
	throw new InvalidArgumentException("Unknow action: $func");
}
