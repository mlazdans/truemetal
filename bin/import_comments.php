<?php
/*
Izdzēst no foruma komentārus
--
DELETE
FROM
	`forum`
WHERE
	`forum_forumid` != 0 AND
	`forum_forumid` NOT IN (
		SELECT `forum_id` FROM `forum_old` WHERE `forum_allowchilds` = 'Y' ORDER BY `forum_id`
	);
*/

$i_am_admin = true;
require_once('../includes/inc.config.php');
require_once('../includes/inc.dbconnect.php');
require_once('lib/utils.php');
include_once('../includes/inc.console.php');

$c_id = 0;
$c_sql = "INSERT INTO `comment` VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');\n";
$cc_sql = "INSERT INTO `comment_connect` VALUES ('%s', '%s', '%s');\n";
$cm_sql = "INSERT INTO `comment_map` VALUES ('%s', '%s', '%s');\n";
$cv_sql = "INSERT INTO `comment_votes` VALUES ('%s', '%s', '%s', '%s', '%s');\n";

printf("SET autocommit=0;\n");
printf("SET unique_checks=0;\n");
printf("TRUNCATE TABLE `comment`;\n");
printf("TRUNCATE TABLE `comment_connect`;\n");
printf("TRUNCATE TABLE `comment_map`;\n");
printf("TRUNCATE TABLE `comment_votes`;\n");

### Forum #####################################################################
$sql = "
SELECT
	*
FROM
	`forum_old`
WHERE
	`forum_forumid` != 0 AND
	`forum_forumid` NOT IN (
		SELECT `forum_id` FROM `forum_old` WHERE `forum_allowchilds` = 'Y' ORDER BY `forum_id`
	)
";
$q = $db->Query($sql);
while($r = $db->FetchObject($q))
{
	$db->QuoteObject($r);
	$c_id = $r->forum_id;

	# Comments
	printf($c_sql,
		$c_id,
		$r->forum_userid,
		$r->forum_userlogin,
		$r->forum_username,
		$r->forum_useremail,
		$r->forum_data,
		$r->forum_datacompiled,
		$r->forum_active,
		$r->forum_userip,
		$r->forum_entered,
		$r->forum_hash,
		$r->forum_hash_date,
		$r->forum_votes
		);

	# Connect
	printf($cc_sql,
		'forum',
		$r->forum_forumid,
		$c_id
		);

	# Votes
	$sql = "SELECT * FROM `comment_votes_old` WHERE `cv_cat` = 'for' AND `cv_catid` = $c_id";
	$qv = $db->Query($sql);
	while($rv = $db->FetchObject($qv))
	{
		printf($cv_sql,
			$c_id,
			$rv->cv_userid,
			$rv->cv_value,
			$rv->cv_userip,
			$rv->cv_entered
			);
	}
}






### Article ###################################################################
$sql = "SELECT * FROM `article_comments_lv_old` ORDER BY `ac_id`";
$q = $db->Query($sql);
while($r = $db->FetchObject($q))
{
	$c_id++;
	$db->QuoteObject($r);

	# Comments
	printf($c_sql,
		$c_id,
		$r->ac_userid,
		$r->ac_userlogin,
		$r->ac_username,
		$r->ac_useremail,
		$r->ac_data,
		$r->ac_datacompiled,
		$r->ac_visible,
		$r->ac_userip,
		$r->ac_entered,
		$r->ac_hash,
		$r->ac_hash_date,
		$r->ac_votes
		);

	# Connect
	printf($cc_sql,
		'article',
		$r->ac_artid,
		$c_id
		);

	# Map
	printf($cm_sql,
		'article',
		$r->ac_id,
		$c_id
		);

	# Votes
	$sql = "SELECT * FROM `comment_votes_old` WHERE `cv_cat` = 'com' AND `cv_catid` = $r->ac_id";
	$qv = $db->Query($sql);
	while($rv = $db->FetchObject($qv))
	{
		printf($cv_sql,
			$c_id,
			$rv->cv_userid,
			$rv->cv_value,
			$rv->cv_userip,
			$rv->cv_entered
			);
	}
}

printf("SET unique_checks=1;\n");
printf("COMMIT;\n");

