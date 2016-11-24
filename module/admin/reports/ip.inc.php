<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Article.php');
require_once('lib/Forum.php');
require_once('lib/Comment.php');
require_once('lib/ResComment.php');

$data = postget('ips', array());
$ips = array_filter(preg_split("/[\s,]/", $data), 'is_not_empty');
if(!$ips)
	return;

$template->set_var('ips', $data, 'BLOCK_report_ip');

$template->set_file('FILE_comment_list', 'comment/list.tpl');
$template->copy_block('BLOCK_report_comments', 'FILE_comment_list');

$RC = new ResComment();
$RC->setDb($db);
$comments = $RC->get(array(
	'ips'=>$ips,
	'c_visible'=>Res::STATE_ALL,
	'sort'=>'c_entered DESC',
	'limit'=>1000,
	));

include('module/admin/comment/list.inc.php');

