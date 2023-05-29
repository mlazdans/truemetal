<?php

error_reporting(E_ALL);

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');

# LOGINS
$sql = "SELECT * FROM `comment` WHERE `c_id` = 200477";

$q = $db->Query($sql);
while($item = $db->FetchAssoc($q))
{
	print "$item[c_id]\n";
	$item['c_datacompiled'] = $item['c_data'];
	parse_text_data($item['c_datacompiled']);
	printr($item['c_datacompiled']);
}

