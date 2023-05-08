<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

$art_per_page = 10;

# GET/POST
$art_id = array_shift($sys_parameters);
$action = post('action');
$hl = get("hl");
$page = ($art_id == 'page' ? (int)array_shift($sys_parameters) : 0);
$art_id_urlized = rawurldecode($art_id??"");
$art_id = (int)$art_id;

# Template
$template = new MainModule($sys_module_id);
$T = $template->add_file('article.tpl');

# Title
$art_title = '';
if(isset($_pointer['_data_']['module_name']))
{
	$art_title = $_pointer['_data_']['module_name'];
} else {
	$art_title = "Jaunumi";
}

# Loading
$tc = 0;
$tp = 0;

// if(empty($_pointer['_data_']['module_name'])){
// 	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
// 	$T->enable('BLOCK_noarticle');
// 	return;
// }

$tableName = "view_mainpage";

// if($i_am_admin){
// 	$tableName = "view_mainpage3";
// }

if($art_id)
{
	$art_name_urlized = '';
	$sql = "SELECT * FROM `$tableName` WHERE `art_id` = $art_id";
	//$sql = "SELECT * FROM ".view_mainpage()." WHERE `art_id` = $art_id";
	if($art = $db->ExecuteSingle($sql))
	{
		# NOTE: redirektējam uz jaunajām adresēm, pēc gada (2011-04-30) varēs noņemt
		$art_name_urlized = urlize($art['art_name']);
		$test_urlized = "$art_id-$art_name_urlized";
		if($art_name_urlized && ($test_urlized != $art_id_urlized))
		{
			$qs = (empty($_SERVER['QUERY_STRING']) ? "" : "?".$_SERVER['QUERY_STRING']);
			$new_url = "$module_root/$test_urlized$qs";
			header("Location: $new_url", true, 301);
			return;
		}
		#
	}

	if($hl)
	{
		hl($art['art_intro'], $hl);
		hl($art['art_data'], $hl);
	}

	$articles = ($art ? array($art) : array());

	$art_title .= (isset($art['art_name']) ? " - ".$art['art_name'] : "");
} elseif(isset($_pointer['_data_']['mod_id'])) {
	$sql = "SELECT COUNT(*) AS cc FROM `$tableName`";
	if($sys_module_id != 'article'){
		$sql .= " WHERE module_id = '$sys_module_id'";
	}
		//printr($sql);
	//$sql = "SELECT COUNT(*) AS cc FROM ".view_mainpage()." WHERE `module_id` = '$sys_module_id'";
	//printr($sql);
	$cc = $db->ExecuteSingle($sql);
	$tc = (int)$cc['cc'];
	//printr($tc);

	$tp = ceil($tc / $art_per_page);
	$art_align = $tc % $art_per_page;

	if( $page && (($page < 0) || ($page >= $tp)) )
	{
		header("Location: $module_root/");
		return;
	}

	if($page)
		$limit = (($tp - $page - 1) * $art_per_page + $art_align).",$art_per_page";
	else
		$limit = $art_per_page;

	$sql = "SELECT * FROM `$tableName`";
	if($sys_module_id != 'article'){
		$sql .= " WHERE module_id = '$sys_module_id'";
	}

	$sql .= " ORDER BY art_entered DESC";
	$sql .= " LIMIT $limit";


	//$sql = "SELECT * FROM ".view_mainpage()." WHERE `module_id` = '$sys_module_id' LIMIT $limit";
	$articles = $db->Execute($sql);
} else {
	$articles = array();
}

# Comments
if($art_id && isset($articles[0]))
{
	$T->enable('BLOCK_article_comments_head');

	$C = $template->add_file('comments.tpl');

	$error_msg = [];
	if($action == 'add_comment')
	{
		if(!user_loged()){
			$template->not_logged();
			return null;
		}

		$res_id = $art['res_id'];
		$data = post('data');
		$C->set_array(specialchars($data));

		if(empty($data['c_data'])){
			$error_msg[] = "Kaut kas jau jāieraksta";
		}

		if(!$error_msg){
			if($ac_id = add_comment($db, $res_id, $data['c_data']))
			{
				$np = join('/', array_keys($path));
				header("Location: /$np/$art_id-$art_name_urlized#comment$ac_id");
				return;
			} else {
				$error_msg[] = "Neizdevās pievienot komentāru";
			}
		}
	}
	#

	if($error_msg) {
		$C->enable('BLOCK_comment_error')->set_var('error_msg', join("<br>", $error_msg));
	}

	Res::markCommentCount($articles[0]);
	$comments = get_res_comments((int)$art['res_id']);
	comment_list($C, $comments, $hl);
	$T->set_block_string('BLOCK_article_comments_head', $C->parse());
}

# Pages
if($tc)
{
	$T->enable('BLOCK_article_page');

	if($page)
	{
		if($page == $tp){
			$T->enable('BLOCK_article_page_next');
			$T->set_var('page', '', 'BLOCK_article_page_next');
		} else if($page < $tp){
			$T->enable('BLOCK_article_page_next');
			$T->set_var('page', "$module_root/page/".($page + 1)."/", 'BLOCK_article_page_next');
		}

		if($page > 1){
			$T->enable('BLOCK_article_page_prev');
			$T->set_var('page', "$module_root/page/".($page - 1)."/", 'BLOCK_article_page_prev');
		}
	} else {
		$T->enable('BLOCK_article_page_prev');
		$T->set_var('page', "$module_root/page/".($tp - 1)."/", 'BLOCK_article_page_prev');
	}

	if($page)
	{
		$T->parse_block('BLOCK_article_page');
		$T->set_var('article_page_top', $T->get_parsed_content('BLOCK_article_page'));
	}
}

if($page && ($page <= $tp))
	$art_title .= sprintf(" %d. lapa ", $page);

if($articles)
{
	if(!$art_id)
		$T->set_var('block_middle_class', 'light');

	$module = new Module;
	$T->enable('BLOCK_article');
	$c = 0;
	foreach($articles as $item)
	{
		++$c;

		$item['art_date'] = date('d.m.Y', strtotime($item['art_entered']));

		# Nočeko datus/intro
		if($item['table_id'] == Table::FORUM)
		{
			if($item['type_id']){
				$intro = mb_substr($item['art_intro'], 0, 200);
				$intro = specialchars($intro);
				if(mb_strlen($item['art_intro']) > 300){
					$intro .= "...";
				}
				$item['art_intro'] = $intro;
				$item['art_data'] = '';
			} else {
				$data_parts = preg_split("/<hr(\s+)?\/?>/", $item['art_data']);

				if(isset($data_parts[0]))
					$item['art_intro'] = $data_parts[0];

				if(isset($data_parts[1]))
				{
					$item['art_data'] = $data_parts[1];
				} else {
					$item['art_data'] = '';
				}
			}
		}

		if($item['art_data'])
		{
			$T->enable('BLOCK_art_cont');
		} else {
			$T->disable('BLOCK_art_cont');
		}

		if($art_id)
		{
			$item['art_date_f'] = proc_date($item['art_entered']);
			$T->enable('BLOCK_art_date_formatted');
			$T->enable('BLOCK_art_data');
			$T->disable('BLOCK_art_intro');
		}
		$T->enable_if(Res::hasNewComments($item), 'BLOCK_comments_new');

		$item['art_name_urlized'] = rawurlencode(urlize($item['art_name']));
		$T->set_array($item, 'BLOCK_article');

		# XXX: fix module_id
		if($item['table_id'] == Table::FORUM)
		{
			$T->set_var('module_id', "forum", 'BLOCK_article');
		} else {
			$T->set_var('module_id', $item['module_id'], 'BLOCK_article');
		}

		$T->parse_block('BLOCK_article', TMPL_APPEND);
	}
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$T->enable('BLOCK_noarticle');
}

$art_title .= ($hl ? sprintf(" - meklēšana: %s", $hl) : "");

$template->set_title($art_title);
if(isset($_pointer['_data_']['module_id']))
	$template->Index->set_var("menu_active_".$_pointer['_data_']['module_id'], "_over");

$template->set_right_defaults();
$template->out($T);
