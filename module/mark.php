<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('lib/MainModule.php');
require_once('lib/Article.php');

$article = new Article;
$articles = $article->load();

if(count($articles)) {
	foreach($articles as $item)
		$_SESSION['comments']['viewed'][$item['art_id']] = $item['art_comment_count'];
}

header("Location: $sys_http_root/");

