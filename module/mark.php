<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/MainModule.php');
require_once('lib/Article.php');

/*
$article = new Article;
$articles = $article->load();

if(count($articles)) {
	foreach($articles as $item)
		$_SESSION['comments']['viewed'][$item['art_id']] = $item['art_comment_count'];
}
*/

$now = time();

$_SESSION['forums']['viewed'] = array();
$_SESSION['comments']['viewed'] = array();

$_SESSION['forums']['viewed_before'] = $now;
$_SESSION['comments']['viewed_before'] = $now;

header("Location: $sys_http_root/");

