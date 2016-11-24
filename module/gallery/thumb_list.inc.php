<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$template->enable('BLOCK_thumb_list');

$gal_cache = "templates/gallery/$gal_id.html";
if(false && file_exists($gal_cache)) {
	$data = join('', file($gal_cache));
	$template->set_block_string('BLOCK_thumb', $data);
	return;
}

# ielasam thumbus
$c = 0;
$data = $GD->load(array('gal_id'=>$gal_id));
$thumb_count = count($data);
foreach($data as $thumb)
{
	++$c;
	if($c % $tpr == 1)
		$template->enable('BLOCK_tr1');
	else
		$template->disable('BLOCK_tr1');
	if(($c % $tpr == 0) || ($c == $thumb_count))
		$template->enable('BLOCK_tr2');
	else
		$template->disable('BLOCK_tr2');
	//$template->set_var('gd_id', $thumb['gd_id'], 'BLOCK_thumb');

	$hash = cache_hash($thumb['gd_id']."thumb.jpg");
	if($CACHE_ENABLE && cache_exists($hash)){
		$template->set_var('thumb_path', cache_http_path($hash), 'BLOCK_thumb');
	} else {
		$template->set_var('thumb_path', "$module_root/thumb/$thumb[gd_id]/", 'BLOCK_thumb');
	}
	$template->{(GalleryData::hasNewComments($thumb) ? "enable" : "disable")}('BLOCK_comments_new');

	//$thumb['res_votes'] = (int)$thumb['res_votes'];
	if($thumb['res_votes'] > 0)
	{
		$template->set_var('comment_vote_class', 'plus', 'BLOCK_thumb');
		$thumb['res_votes'] = '+'.$thumb['res_votes'];
	} elseif($thumb['res_votes'] < 0) {
		$template->set_var('comment_vote_class', 'minus', 'BLOCK_thumb');
	} else {
		$template->set_var('comment_vote_class', '', 'BLOCK_thumb');
	}
	$template->set_array($thumb, 'BLOCK_thumb');
	$template->parse_block('BLOCK_thumb', TMPL_APPEND);
}

/*
if(false){
	save_data($gal_cache, $template->get_block('BLOCK_thumb')->parse());
}
*/

