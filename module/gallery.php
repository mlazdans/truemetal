<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/ResComment.php');
require_once('lib/Gallery.php');
require_once('lib/GalleryData.php');
require_once('lib/MainModule.php');

$CACHE_ENABLE = true;
$gal_id = array_shift($sys_parameters);
$gd_id = (int)array_shift($sys_parameters);
$hl = rawurldecode(get("hl"));
$action = post('action');

function gallery_error($msg, &$template) {
	$template->enable('BLOCK_gallery_error');
	$template->set_var('error_msg', $msg);
}

$GD = new GalleryData;
$gallery = new Gallery;

# thumbs per row
$tpr = 5;

if(($gal_id == 'thumb' || $gal_id == 'image') && $gd_id)
{
	$hash = cache_hash($gd_id.$gal_id.".jpg");
	if($CACHE_ENABLE && cache_exists($hash)){
		$jpeg = cache_read($hash);
	} else {
		$data = $GD->load(array(
			'gd_id'=>$gd_id,
			'load_images'=>true,
			));

		$jpeg = $gal_id == 'image' ? $data['gd_data'] : $data['gd_thumb'];
		cache_save($hash, $jpeg);
	}

	header('Content-type: image/jpeg');
	print $jpeg;
	exit;
}

$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_gallery', 'gallery.tpl');
$template->copy_block('BLOCK_middle', 'FILE_gallery');

if(!user_loged())
{
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden");
	gallery_error("TrueMetal!", $template);
} elseif($gal_id) {
# ja skataas bildi, nocheko vai attieciigaa galerija ir pieejama
	if($gal_id == 'view' && $gd_id)
	{
		$galdata = $GD->load($gd_id);
		if(!isset($galdata['gal_id'])) {
			header("Location: $module_root/");
			exit;
		}
		$gal = $gallery->load($galdata['gal_id']);
	} else {
		$gal = $gallery->load($gal_id);
	}

	if(!isset($gal['gal_id'])) {
		header("Location: $module_root/");
		exit;
	}

	$gal_name = "";
	if($gal['gal_ggid'])
		$gal_name .= "$gal[gg_name] / ";
	$gal_name .= "$gal[gal_name]";

	$template->set_var('gal_name', $gal_name);
	$template->set_var('gal_id', $gal['gal_id']);
	$template->set_title('Galerija '.$gal_name);

	if($gal['gal_ggid']){
		$template->set_var('gal_jump_id', "gg_".$gal['gal_ggid']);
	} else {
		$template->set_var('gal_jump_id', "gal_".$gal['gal_id']);
	}

	if($gal_id == 'view')
	{
		$gal_id = $gal['gal_id'];
		# Komenti
		//$_SESSION['gallery']['viewed'][$gal_id] = $forum_data['forum_comment_count'];

		if(user_loged() && ($action == 'add_comment'))
		{
			$res_id = $galdata['res_id'];
			$data = post('data');
			$resDb = $db;
			if($c_id = include('module/comment/add.inc.php'))
			{
				$resDb->Commit();
				header("Location: $sys_http_root/resroute/$res_id/#comment$c_id");
				return;
			}
		}

		$template->set_file('FILE_gallery_comments', 'comments.tpl');
		$template->copy_block('BLOCK_gallery_comments', 'FILE_gallery_comments');

		$RC = new ResComment();
		$params = array(
			'res_id'=>$galdata['res_id'],
			);
		$params['order'] =
			isset($_SESSION['login']['l_forumsort_msg']) &&
			($_SESSION['login']['l_forumsort_msg'] == FORUM_SORT_DESC)
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
	} else {
		$template->enable('BLOCK_thumb_list');
		$gal_cache = "templates/gallery/$gal_id.html";
		if(false && file_exists($gal_cache)) {
			$data = join('', file($gal_cache));
			$template->set_block_string('BLOCK_thumb', $data);
		} else {
			# ielasam thumbus
			$data = $GD->load(array('gal_id'=>$gal_id));
			$thumb_count = count($data);
			$c = 0;
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
				$template->set_var('gd_id', $thumb['gd_id'], 'BLOCK_thumb');

				$hash = cache_hash($thumb['gd_id']."thumb.jpg");
				if($CACHE_ENABLE && cache_exists($hash)){
					$template->set_var('thumb_path', cache_http_path($hash), 'BLOCK_thumb');
				} else {
					$template->set_var('thumb_path', "$module_root/thumb/$thumb[gd_id]/", 'BLOCK_thumb');
				}

				$template->parse_block('BLOCK_thumb', TMPL_APPEND);
			}

			/*
			if(false){
				save_data($gal_cache, $template->get_block('BLOCK_thumb')->parse());
			}
			*/
		}
	}
} else {
	# ielasam galerijas
	$template->set_title('Galerijas');
	$gal_cache = "templates/gallery/gallery.html";
	if(false && file_exists($gal_cache)) {
		$template->enable('BLOCK_gallery_list');
		$data = join('', file($gal_cache));
		$template->set_block_string('BLOCK_gallery_list', $data);
	} elseif($data = $gallery->load()) {
		$template->enable('BLOCK_gallery_list');

		$data2 = array();
		foreach($data as $gal) {
			$k = empty($gal['gal_ggid']) ? "e-".$gal['gal_id'] : $gal['gal_ggid'];
			$data2[$k][] = $gal;
		}

		foreach($data2 as $gal_ggid=>$data)
		{
			$template->set_array($data[0], 'BLOCK_gallery_list');
			if($data[0]['gal_ggid']){
				$template->set_var('gg_name', $data[0]['gg_name'], 'BLOCK_gallery_group');
				$template->set_var('gal_jump_id', "gg_".$data[0]['gg_id'], 'BLOCK_gallery_group');
			} else {
				$template->set_var('gg_name', $data[0]['gal_name'], 'BLOCK_gallery_group');
				$template->set_var('gal_jump_id', "gal_".$data[0]['gal_id'], 'BLOCK_gallery_group');
			}

			foreach($data as $gal){
				$template->set_array($gal, 'BLOCK_gallery_data');
				$template->parse_block('BLOCK_gallery_data', TMPL_APPEND);
			}
			$template->parse_block('BLOCK_gallery_list', TMPL_APPEND);
		}
		/*
		if(false){
			save_data($gal_cache, $template->get_block('BLOCK_gallery')->parse());
		}
		*/
	} else {
		gallery_error($gallery->error_msg, $template);
	}
}

$template->set_right();
$template->set_recent_forum();
$template->set_login();
$template->set_online();
$template->set_search();
$template->set_jubilars();
$template->out();
