<?php declare(strict_types = 1);

require_once('../include/boot.php');
require_once('include/dbconnect.php');

ini_set('session.use_cookies', false);

$data = 'login|a:19:{s:4:"l_id";s:1:"3";s:6:"l_hash";s:8:"1264e7e9";s:7:"l_login";s:8:"marrtins";s:6:"l_nick";s:7:"BigUgga";s:7:"l_email";s:19:"marrtins@hackers.lv";s:8:"l_active";s:1:"Y";s:10:"l_accepted";s:1:"Y";s:9:"l_entered";s:19:"2004-09-26 05:38:59";s:8:"l_userip";s:15:"193.108.185.175";s:14:"l_emailvisible";s:1:"Y";s:12:"l_lastaccess";s:19:"2023-05-13 00:13:37";s:9:"l_logedin";s:1:"Y";s:18:"l_forumsort_themes";s:1:"C";s:15:"l_forumsort_msg";s:1:"A";s:17:"l_disable_avatars";s:1:"0";s:17:"l_disable_youtube";s:1:"0";s:10:"votes_plus";s:5:"12060";s:11:"votes_minus";s:3:"794";s:13:"comment_count";s:5:"10500";}forums|a:1:{s:11:"viewed_date";a:1:{i:1;s:19:"2023-03-24 16:47:20";}}64364:"comments|N;user|a:2:{s:8:"username";s:7:"BigUgga";s:9:"useremail";s:19:"marrtins@hackers.lv";}res|a:2:{s:13:"viewed_before";i:1683429300;s:11:"viewed_date";a:30:{i:293905;s:19:"2023-05-12 22:06:43";i:293836;s:19:"2023-05-06 13:50:39";i:241972;s:19:"2023-04-13 20:51:37";i:291006;s:19:"2022-12-01 15:46:58";i:242293;s:19:"2007-03-15 09:38:41";i:243685;s:19:"2023-05-11 17:23:05";i:293847;s:19:"2023-04-24 13:03:03";i:293858;s:19:"2023-05-04 19:40:47";i:247941;s:19:"2023-05-09 12:23:12";i:256107;s:19:"2023-05-09 12:52:36";i:293952;s:19:"2023-05-10 14:55:39";i:293885;s:19:"2023-05-06 00:54:10";i:263076;s:19:"2019-06-19 22:25:49";i:267371;s:19:"2023-05-12 13:38:45";i:246954;s:19:"2009-12-07 12:36:51";i:246416;s:19:"2011-12-31 16:31:59";i:293845;s:19:"2023-04-24 12:55:59";i:293100;s:19:"2023-03-02 18:19:07";i:257417;s:19:"2015-01-30 15:39:27";i:247107;s:19:"2010-01-23 22:15:02";i:275120;N;i:275121;N;i:275122;N;i:279447;N;i:279448;N;i:279449;N;i:280686;N;i:280690;N;i:280877;N;i:241913;s:19:"2020-07-21 08:19:59";}}';

session_start();
session_decode($data);

var_dump($_SESSION);
