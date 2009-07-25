<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.MainModule.php');
require_once('../classes/class.Article.php');
require_once('../classes/class.Comment.php');

$article = new Article;
$article->set_limit(0);
$articles = $article->load();

$comment = new Comment;

if(count($articles)) {
	foreach($articles as $item) {
		$comment_count = $comment->comment_count($item['art_id']);
		$_SESSION['comments']['viewed'][$item['art_id']] = $comment_count;
	}
}

header("Location: $sys_http_root/");
