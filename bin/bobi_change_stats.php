<?php

$KERNEL_LEAVE_AFTER_INIT = true;
require_once('../public/kernel.php');
require_once('include/console.php');
require_once('include/dbconnect.php');
require_once('lib/utils.php');
require_once('lib/Logins.php');

$last = array();
$changes = array();
$sql = "SELECT * FROM `logins_change`";
$q = $db->Query($sql);
while($item = $db->FetchAssoc($q))
{
	if(isset($last[$item['l_id']]))
	{
		$k = $item['l_id'];
		if(!isset($changes[$k]))
			$changes[$k] = 0;

		if($last[$item['l_id']] != $item['l_disable_bobi'])
		{
			$changes[$k]++;
		}
	}
	$last[$item['l_id']] = $item['l_disable_bobi'];
}

arsort($changes, SORT_NUMERIC);

$c = 0;
foreach($changes as $l_id=>$count)
{
	$c++;
	$login = Logins::load_by_id($l_id);
	print "$login[l_nick] $count\n";
	if($c == 10)
		break;
}

