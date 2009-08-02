<?php

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

$db->AutoCommit(false);

# LOGINS
$sql = "SELECT * FROM `comment`";
$q = $db->Query($sql);
while($item = $db->FetchAssoc($q))
{
	if(($item['c_id'] % 1000 == 0))
		print "$item[c_id]\n";
	$item['c_datacompiled'] = $item['c_data'];
	parse_text_data($item['c_datacompiled']);
	$db->Execute("UPDATE `comment` SET `c_datacompiled` = '$item[c_datacompiled]' WHERE `c_id` = $item[c_id]");
}

$db->Commit();

