<?php

/*
91.90.225.10,
87.110.33.35,
195.13.213.98,
*/

require_once('lib/Article.php');
require_once('lib/Forum.php');
require_once('lib/Comment.php');
require_once('lib/CommentConnect.php');

$data = post('data', array());
$ips = array_filter(preg_split("/[\s,]/", $data['ips']), 'is_not_empty');
if(!$ips)
	return;

$template->set_var('ips', $data['ips'], 'BLOCK_report_ip');
//$ip_sql = "'".join("','", $ip)."'";

$template->set_file('FILE_comment_list', 'comment/list.tpl');
$template->copy_block('BLOCK_report_comments', 'FILE_comment_list');

$CC = new CommentConnect();
$CC->setDb($db);
$comments = $CC->get(array(
	'ips'=>$ips,
	'c_visible'=>COMMENT_ALL,
	));

include("module/admin/comment/list.inc.php");

/*
$sql = "SELECT * FROM article_comments_lv WHERE ac_userip IN ($ip_sql)";
$data = $db->Execute($sql);

print "Articles:\n";
printr($data);
print "\n";

$sql = "SELECT * FROM forum WHERE forum_userip IN ($ip_sql)";
$data = $db->Execute($sql);

print "Forum:\n";
printr($data);
print "\n";

$sql = "SELECT * FROM logins WHERE l_userip IN ($ip_sql)";
$data = $db->Execute($sql);

print "Logins:\n";
printr($data);
print "\n";
*/
