<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

require_once('lib/Article.php');
require_once('lib/Module.php');
require_once('lib/ResComment.php');

$art_per_page = 10;

# Title
$art_title = '';
if($_pointer['_data_']['module_name'])
{
	$art_title = $_pointer['_data_']['module_name'];
} else {
	$art_title = "Jaunumi";
}

# GET/POST
$art_id = array_shift($sys_parameters);
$action = post('action');
$hl = get("hl");
$page = ($art_id == 'page' ? (int)array_shift($sys_parameters) : 0);
$art_id_urlized = rawurldecode($art_id);
$art_id = (int)$art_id;

# Template
$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_article', 'article.tpl');
$template->copy_block('BLOCK_middle', 'FILE_article');

# Loading

$tc = 0;
$tp = 0;

if($art_id)
{
	$art_name_urlized = '';
	if($art = $db->ExecuteSingle("SELECT * FROM `view_mainpage` WHERE `art_id` = $art_id"))
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
} elseif($_pointer['_data_']['mod_id']) {
	$cc = $db->ExecuteSingle("SELECT COUNT(*) AS cc FROM `view_mainpage` WHERE `module_id` = '$sys_module_id'");
	$tc = (int)$cc['cc'];

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

	$sql = "SELECT * FROM `view_mainpage` WHERE `module_id` = '$sys_module_id' LIMIT $limit";
	$articles = $db->Execute($sql);
} else {
	$articles = array();
}

# Comments
if($art_id && isset($articles[0]))
{
	$template->set_file('FILE_article_comments', 'comments.tpl');
	$template->copy_block('BLOCK_article_comments', 'FILE_article_comments');
	$template->enable('BLOCK_article_comments_head');

	# Add
	if(($action == 'add_comment') && user_loged())
	{
		$res_id = $art['res_id'];
		$data = post('data');
		$resDb = $db;
		if($ac_id = include('module/comment/add.inc.php'))
		{
			$resDb->Commit();
			$np = join('/', array_keys($path));
			header("Location: $sys_http_root/$np/$art_id-$art_name_urlized#comment$ac_id");
			return;
		}
	}
	#

	Res::markCommentCount($articles[0]);

	$RC = new ResComment();
	$comments = $RC->Get(array(
		'res_id'=>$art['res_id'],
		));

	include('comment/list.inc.php');
}

# Pages
if($tc)
{
	$template->enable('BLOCK_article_page');

	if($page)
	{
		if($page == $tp){
			$template->enable('BLOCK_article_page_next');
			$template->set_var('page', '', 'BLOCK_article_page_next');
		} else if($page < $tp){
			$template->enable('BLOCK_article_page_next');
			$template->set_var('page', "$module_root/page/".($page + 1)."/", 'BLOCK_article_page_next');
		}

		if($page > 1){
			$template->enable('BLOCK_article_page_prev');
			$template->set_var('page', "$module_root/page/".($page - 1)."/", 'BLOCK_article_page_prev');
		}
	} else {
		$template->enable('BLOCK_article_page_prev');
		$template->set_var('page', "$module_root/page/".($tp - 1)."/", 'BLOCK_article_page_prev');
	}

	if($page)
	{
		$template->parse_block('BLOCK_article_page');
		$template->set_var('article_page_top', $template->get_parsed_content('BLOCK_article_page'));
	}
}

if($page && ($page <= $tp))
	$art_title .= sprintf(" %d. lapa ", $page);

if($articles)
{
	if(!$art_id)
		$template->set_var('block_middle_class', 'light');

	$module = new Module;
	$template->enable('BLOCK_article');
	$c = 0;
	foreach($articles as $item)
	{
		++$c;

		$item['art_date'] = date('d.m.Y', strtotime($item['art_entered']));

		# Nočeko datus/intro
		if($item['table_id'] == Table::FORUM)
		{
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

		if($item['art_data'])
		{
			$template->enable('BLOCK_art_cont');
		} else {
			$template->disable('BLOCK_art_cont');
		}

		if($art_id)
		{
			$item['art_date_f'] = proc_date($item['art_entered']);
			$template->enable('BLOCK_art_date_formatted');
			$template->enable('BLOCK_art_data');
			$template->disable('BLOCK_art_intro');
		}
		$template->{(Res::hasNewComments($item) ? "enable" : "disable")}('BLOCK_comments_new');

		$item['art_name_urlized'] = rawurlencode(urlize($item['art_name']));
		$template->set_array($item, 'BLOCK_article');

		# XXX: fix module_id
		if($item['table_id'] == Table::FORUM)
		{
			$template->set_var('module_id', "forum", 'BLOCK_article');
		} else {
			$template->set_var('module_id', $item['module_id'], 'BLOCK_article');
		}

		$template->parse_block('BLOCK_article', TMPL_APPEND);
	}
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$template->enable('BLOCK_noarticle');
}

$art_title .= ($hl ? sprintf(" - meklēšana: %s", $hl) : "");

$template->set_title($art_title);

$template->set_right();
$template->set_events();
$template->set_login();
$template->set_online();
$template->set_jubilars();
$template->set_recent_comments();
$template->set_search();
$template->set_recent_reviews();

if($art_id && $art)
{
	$template->set_descr((empty($hl) ? "" : sprintf("(Meklēšana: %s) ", trim($hl))).$art["art_name"]." - ".$art["art_intro"].' '.$art["art_data"]);
} else {
	$template->set_descr(htmlspecialchars($_pointer['_data_']['module_descr']));
}

$template->set_var("menu_active_".$_pointer['_data_']['module_id'], "_over");
$template->out();

