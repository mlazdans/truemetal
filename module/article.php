<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// There are 1065 DOM elements on the page

require_once('lib/MainModule.php');
require_once('lib/Article.php');
require_once('lib/Module.php');
require_once('lib/CommentConnect.php');

$art_per_page = 10;

# GET/POST
$art_id = array_shift($sys_parameters);
$action = post('action');
$hl = urldecode(get("hl"));
$page = ($art_id == 'page' ? (int)array_shift($sys_parameters) : 0);
$art_id = (int)$art_id;

# Template
$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_article', 'article.tpl');
$template->copy_block('BLOCK_middle', 'FILE_article');
if($art_id)
{
	$template->set_file('FILE_article_comments', 'comments.tpl');
	$template->copy_block('BLOCK_article_comments', 'FILE_article_comments');
}

if(!$art_id)
	$template->set_var('block_middle_class', 'light');

# Loading
$article = new Article();

$tc = 0;
if($art_id)
{
	$template->enable('BLOCK_article_comments_head');

	$art = $article->load(array(
		'art_id'=>$art_id
		));

	if($hl)
	{
		hl($art['art_intro'], $hl);
		hl($art['art_data'], $hl);
	}

	$articles = ($art ? array($art) : array());
	$item = $art;

	if(($action == 'add_comment') && ($item['art_comments'] == ARTICLE_COMMENTS) && user_loged())
	{
		$table = 'article';
		$table_id = $art_id;
		$data = post('data');
		if($ac_id = include('module/comment/add.inc.php'))
		{
			$db->Commit();
			$np = join('/', array_keys($path));
			header("Location: $sys_http_root/$np/$art_id/#comment$ac_id");
			return;
		}
	}
} elseif($_pointer['_data_']['mod_id']) {
	$tc = $article->get_total($_pointer['_data_']['mod_id']);
	$tp = ceil($tc / $art_per_page);
	$art_align = $art_per_page - ($tc - $tp * $art_per_page);
	//print "tc=$tc; tp=$tp; align=$art_align";

	if( $page && (($page < 0) || ($page >= $tp)) )
	{
		header("Location: $module_root/");
		return;
	}

	if($page)
		$limit = (($tp - $page + 1) * $art_per_page - $art_align).",$art_per_page";
	else
		$limit = $art_per_page;

	$articles = $article->load(array(
		'art_modid'=>$_pointer['_data_']['mod_id'],
		'limit'=>$limit,
		));
} else {
	$articles = array();
}

// no chekojam vai registreeta *sadalja/raksts*
# Comments
if($art_id)
{
	$_SESSION['comments']['viewed'][$art_id] = $articles[0]['art_comment_count'];
	$CC = new CommentConnect('article');
	$CC->setDb($db);
	$comments = $CC->get(array(
		'cc_table_id'=>$art_id,
		));
	include("comment/list.inc.php");
}

# Pages
if($tc)
{
	$template->enable('BLOCK_article_page');

	if(!$page)
	{
		$template->enable('BLOCK_article_page_prev');
		$template->set_var('page', "$module_root/page/".($tp - 1)."/", 'BLOCK_article_page_prev');
	} else {
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
	}
}

# Title
$art_title = '';
if($_pointer['_data_']['module_name'])
{
	$art_title = $_pointer['_data_']['module_name'].($hl ? sprintf(", meklēšana: %s", $hl) : "");
} else {
	$art_title = 'Jaunumi';
}

if($page && ($page <= $tp))
	$art_title .= sprintf(" %d. lapa ", $page);

$template->set_title($art_title);

if(count($articles))
{
	$module = new Module;
	$template->enable('BLOCK_article');
	//$template->set_ad();
	$c = 0;
	foreach($articles as $item)
	{
		++$c;

		$item['art_date'] = date('d.m.Y', strtotime($item['art_entered']));
		if($art_id)
		{
			//$patt = '/(.*)(<hr\s+id="editor_splitter" \/>)(.*)/ims';
			//$item['art_data'] = preg_replace($patt, '<div style="font-weight: bold;">\1</div><hr/>\3', $item['art_data'], 1);
			$item['art_date_f'] = proc_date($item['art_entered']);
			$item['art_data_display'] = $item['art_intro'].$item['art_data'];
			$template->enable('BLOCK_art_date_formatted');
			$template->enable('BLOCK_art_data');
		} else {
			//$patt = '/<hr\s+id="editor_splitter" \/>.*/ims';

			$template->enable('BLOCK_art_intro');
			if($item['art_data'])
			{
				$item['art_data_display'] = $item['art_intro'];
				$template->enable('BLOCK_art_cont');
			} else {
				$template->disable('BLOCK_art_cont');
			}
			/*
			if(preg_match($patt, $item['art_data']))
			{
				$item['art_data'] = preg_replace($patt, '', $item['art_data'], 1);
				$template->enable('BLOCK_art_cont');
			} else {
				$template->disable('BLOCK_art_cont');
			}
			*/
		}

		/*
		if($item['art_comments'] == ARTICLE_NOCOMMENTS) {
			$template->disable('BLOCK_is_comments');
		} else {
			$template->enable('BLOCK_is_comments');
		}
		*/

		$old_comment_count =
			isset($_SESSION['comments']['viewed'][$item['art_id']]) ?
			$_SESSION['comments']['viewed'][$item['art_id']] :
			0;

		$template->disable('BLOCK_comments_new');
		if($item['art_comment_count'] > $old_comment_count)
		{
			$template->enable('BLOCK_comments_new');
		}

		$template->set_array($item, 'BLOCK_article');
		$template->set_var('art_path', $module->get_path($item['art_modid']), 'BLOCK_article');
		$template->parse_block('BLOCK_article', TMPL_APPEND);
	}
	//$article->set_comment_count($template, $articles);
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$template->enable('BLOCK_noarticle');
}

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_reviews(13);
$template->set_poll();
$template->set_search();

$template->out();

