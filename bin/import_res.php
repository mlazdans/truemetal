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
require_once('lib/Article.php');
require_once('lib/Forum.php');
require_once('lib/Comment.php');
require_once('lib/Res.php');
require_once('lib/Table.php');

error_reporting(E_ALL);
ini_set('memory_limit', -1);

function res_import($item)
{
	global $res_sql;

	printf($res_sql, $item['res_id'], $item['table_id'], $item['login_id'], $item['res_entered']);
} // res_sql

$res_id = 0;

$res_sql = "INSERT INTO `res` (`res_id`, `table_id`, `login_id`, `res_entered`) VALUES (%d, %d, %d, '%s');";

$article_sql = "UPDATE `article` SET `res_id` = %d WHERE `art_id` = %d;";
$forum_sql = "UPDATE `forum` SET `res_id` = %d WHERE `forum_id` = %d;";
$comment_sql = "UPDATE `comment` SET `res_id` = %d WHERE `c_id` = %d;";

printf("SET autocommit=0;\n");
printf("SET unique_checks=0;\n");
printf("UPDATE `article` SET `res_id` = NULL;\n");
printf("UPDATE `forum` SET `res_id` = NULL;\n");
printf("UPDATE `comment` SET `res_id` = NULL;\n");
printf("TRUNCATE TABLE `res_vote`;\n");
printf("TRUNCATE TABLE `res_comment`;\n");
printf("TRUNCATE TABLE `res`;\n");

### Comment ###################################################################
$Comment = new Comment;
$Comment->setDb($db);
$items = $Comment->get(array(
	'c_visible'=>Comment::ALL,
	'sort'=>'c_id ASC',
	));

foreach($items as $item)
{
	$res = array(
		'res_id'=>++$res_id,
		'table_id'=>Table::COMMENT,
		'login_id'=>$item['login_id'],
		'res_entered'=>$item['c_entered'],
		'res_old_id'=>$item['c_id'],
		);
	res_import($res);
	printf($comment_sql, $res['res_id'], $res['res_old_id']);
	print "\n";
}

### Article ###################################################################
$Article = new Article;
$items = $Article->load(array(
	'art_active'=>ARTICLE_ALL,
	'order'=>'a.art_id ASC',
	));

foreach($items as $item)
{
	$res = array(
		'res_id'=>++$res_id,
		'table_id'=>Table::ARTICLE,
		'login_id'=>$item['login_id'],
		'res_entered'=>$item['art_entered'],
		'res_old_id'=>$item['art_id'],
		);
	res_import($res);
	printf($article_sql, $res['res_id'], $res['res_old_id']);
	print "\n";
}

### Forum #####################################################################
$Forum = new Forum;
$items = $Forum->load(array(
	'forum_active'=>FORUM_ALL,
	'order'=>'f.forum_id ASC',
	));

foreach($items as $item)
{
	$res = array(
		'res_id'=>++$res_id,
		'table_id'=>Table::FORUM,
		'login_id'=>$item['forum_userid'],
		'res_entered'=>$item['forum_entered'],
		'res_old_id'=>$item['forum_id'],
		);
	res_import($res);
	printf($forum_sql, $res['res_id'], $res['res_old_id']);
	print "\n";
}

printf("SET unique_checks=1;\n");
printf("COMMIT;\n");

