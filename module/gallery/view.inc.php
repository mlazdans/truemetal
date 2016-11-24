<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

# Komenti
Res::markCommentCount($galdata);

if(user_loged() && ($action == 'add_comment'))
{
	$res_id = $galdata['res_id'];
	$data = post('data');
	$resDb = $db;
	if($c_id = include('module/comment/add.inc.php'))
	{
		$resDb->Commit();
		header("Location: ".GalleryData::Route($galdata, $c_id));
		return;
	}
}

$template->set_file('FILE_gallery_comments', 'comments.tpl');
$template->copy_block('BLOCK_gallery_comments', 'FILE_gallery_comments');

$RC = new ResComment();
$params = array('res_id'=>$galdata['res_id']);
# TODO: izvākt un ielikt kaut kur zem list.inc.php
$params['order'] =
	isset($_SESSION['login']['l_forumsort_msg']) &&
	($_SESSION['login']['l_forumsort_msg'] == Forum::SORT_DESC)
	? "c_entered DESC"
	: "c_entered";
$comments = $RC->Get($params);
include('module/comment/list.inc.php');

# ja skataas pa vienai
$template->enable('BLOCK_image');

$hash = cache_hash($gd_id."image.jpg");
if($CACHE_ENABLE && cache_exists($hash)){
	$template->set_var('image_path', cache_http_path($hash), 'BLOCK_image');
} else {
	$template->set_var('image_path', "$module_root/image/$gd_id/", 'BLOCK_image');
}

$galdata['res_votes'] = (int)$galdata['res_votes'];
if($galdata['res_votes'] > 0)
{
	$template->set_var('comment_vote_class', 'plus', 'BLOCK_image');
	$galdata['res_votes'] = '+'.$galdata['res_votes'];
} elseif($galdata['res_votes'] < 0) {
	$template->set_var('comment_vote_class', 'minus', 'BLOCK_image');
} else {
	$template->set_var('comment_vote_class', '', 'BLOCK_image');
}

# nechekojam, vai ir veel bildes
$next_id = $GD->get_next_data($gal['gal_id'], $gd_id);
$template->set_var('gd_nextid', $next_id ? $next_id : $gd_id, 'BLOCK_image');
$template->set_array($galdata, 'BLOCK_image');

