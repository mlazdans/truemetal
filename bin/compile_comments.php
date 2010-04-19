<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

$db->AutoCommit(false);

# COMMENTS
$sql = "SELECT * FROM `comment`";
$q = $db->Query($sql);
while($item = $db->FetchAssoc($q))
{
	if(($item['c_id'] % 1000 == 0))
		print "Comment: $item[c_id]\n";
	$item['c_datacompiled'] = $item['c_data'];
	parse_text_data($item['c_datacompiled']);
	$db->Execute("UPDATE `comment` SET `c_datacompiled` = '$item[c_datacompiled]' WHERE `c_id` = $item[c_id]");
}

# FORUM
$sql = "SELECT * FROM `forum` WHERE `forum_display` != 1"; // manuāli formatētos ierakstu neaiztiekam (pagaidām)
$q = $db->Query($sql);
while($item = $db->FetchAssoc($q))
{
	if(($item['forum_id'] % 1000 == 0))
		print "Forum: $item[forum_id]\n";
	$item['forum_datacompiled'] = $item['forum_data'];
	parse_text_data($item['forum_datacompiled']);
	$db->Execute("UPDATE `forum` SET `forum_datacompiled` = '$item[forum_datacompiled]' WHERE `forum_id` = $item[forum_id]");
}

$db->Commit();

