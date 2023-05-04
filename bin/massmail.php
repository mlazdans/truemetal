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

$sql_add = '';
//$sql_add = ' AND l_id = 3';
$sql = "SELECT l_nick, l_email FROM `logins` WHERE `l_lastaccess` > '2015-01-01' and l_active='Y' and l_accepted='Y'$sql_add";

$logins = $db->Execute($sql);

$subj = "Pirmais truemetal.lv biedru koppļēgurs!";
$atta = array(
	array(
		'tmp_name'=>'/www/truemetal.lv/public/data/koppljegurs2016.jpg',
		'name'=>'koppljegurs2016.jpg',
		'type'=>'image/jpeg',
		),
	);

//$c = 0;
foreach($logins as $l)
{
	//$c++;
	//$l['l_email'] = 'martinsl@norge.lv';
	$msg = "$l[l_nick]!

25. novembrī, plkst 20:00, klubā Nabaklab truemetal.lv biedru tikšanās - koppļēgurs.

Metāls, alkohols un iespēja skatīt vaigā virtuālos draugus un cirst zemē naideniekus!

Nepalaid garām!

https://truemetal.lv/forum/124498/
";
	email($l['l_email'], $subj, $msg, $atta);
	print $l['l_email']."\n";
	//if($c % 25 == 0)
	//	sleep(5);
}

