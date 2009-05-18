<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// ----------------------------------------------------------------------------

# SELECT * FROM comment_votes WHERE cv_cat = 'for' AND cv_catid = 98554 AND cv_userid = 2520
# TODO: pārlikt uz class
# TODO: par forumu var balsot tikai par komentāriem
# TODO: balsot nevar par saviem komentāriem

$cv_modules_map = array(
	'artcom'=>'com',
	'forum'=>'for',
	);

$value = array_shift($sys_parameters);
$cat = array_shift($sys_parameters);
$cat_id = (int)array_shift($sys_parameters);
$json = array_shift($sys_parameters) == 'json';

# ja kreiss modulis
if(!user_loged() || !isset($cv_modules_map[$cat]))
{
	header("Location: $sys_http_root/");
	return;
}

$user_id = $_SESSION['login']['l_id'];
$cat = $cv_modules_map[$cat];

# FORUM
if($cat == 'for')
{
	require_once('../classes/class.Forum.php');
	$forum = new Forum;
	$forum_data = $forum->load($cat_id);
	$cv_user_id = $forum_data['forum_userid'];
}

# ARTICLE
if($cat == 'com')
{
	require_once('../classes/class.Article.php');
	$article = new Article;
	$comment_data = $article->load_comment($cat_id);
	$cv_user_id = $comment_data['ac_userid'];
}

$ceck_sql = sprintf(
	"SELECT * FROM comment_votes WHERE cv_cat = '%s' AND cv_catid = %d AND cv_userid = %d",
	addslashes($cat),
	$cat_id,
	$user_id
);

$insert_sql = sprintf(
	"INSERT INTO comment_votes (cv_cat, cv_catid, cv_userid, cv_value, cv_entered, cv_userip) VALUES ('%s', %d, %d, %d, NOW(), '%s')",
	addslashes($cat),
	$cat_id,
	$user_id,
	$value == 'up' ? 1 : -1,
	$ip
);

if(($cv_user_id == $user_id) || ($check = $db->ExecuteSingle($ceck_sql)))
{
} else {
	$db->Execute($insert_sql);
}

if($json)
{
	if($cat == 'for')
	{
		$forum = new Forum;
		$item = $forum->load($cat_id);
		$forumVotes = $item['forum_votes'];
	} else if($cat == 'com') {
		$article = new Article;
		$item = $article->load_comment($cat_id);
		$forumVotes = $item['ac_votes'];
	} else {
		# should never get here
		$forumVotes = 0;
	}

	$r = new StdClass;
	$r->forumVotes = $forumVotes;
	print json_encode($r);
} else {
	if($cat == 'for')
	{
		header(sprintf("Location: $sys_http_root/forum/%d/", $forum_data['forum_forumid']));
	} else if($cat == 'com') {
		header(sprintf("Location: $sys_http_root/article/%d/", $comment_data['ac_artid']));
	} else {
		# should never get here
		header("Location: $sys_http_root/");
		break;
	}
}

