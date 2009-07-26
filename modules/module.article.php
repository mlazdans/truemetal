<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// There are 1065 DOM elements on the page

require_once('../classes/class.MainModule.php');
require_once('../classes/class.Article.php');
require_once('../classes/class.Module.php');
require_once('../classes/class.CommentConnect.php');

$art_per_page = 10;

# GET/POST
$art_id = array_shift($sys_parameters);
$action = post('action');
$hl = urldecode(get("hl"));
$page = ($art_id == 'page' ? (int)array_shift($sys_parameters) : 0);
$art_id = (int)$art_id;

# Template
$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_article', 'tmpl.article.php');
$template->copy_block('BLOCK_middle', 'FILE_article');
if($art_id)
{
	$template->set_file('FILE_article_comments', 'tmpl.comments.php');
	$template->copy_block('BLOCK_article_comments', 'FILE_article_comments');
}

if(!$art_id)
	$template->set_var('block_middle_class', 'light');

# Loading
$article = new Article;

$tc = 0;
if($art_id) {
	$template->enable('BLOCK_article_comments_head');
	$articles = (($art = $article->load($art_id)) ? array($art) : array());
	$item = $art;

	if(($action == 'add_comment') && ($item['art_comments'] == ARTICLE_COMMENTS) && user_loged())
	{
		$table = 'article_'.$sys_lang;
		$table_id = $art_id;
		if($ac_id = include('../modules/comment/add.inc.php'))
		{
			$CommentConnect->db->Commit();
			$np = join('/', array_keys($path));
			header("Location: $sys_http_root/$np/$art_id/#comment$ac_id");
			return;
		}
	}
} elseif($_pointer['_data_']['mod_id']) {
	$tc = $article->get_total($_pointer['_data_']['mod_id']);
	$tp = floor($tc / $art_per_page);
	$art_align = $art_per_page - ($tc - $tp * $art_per_page);

	if( $page && (($page < 0) || ($page > $tp)) )
	{
		header("Location: $module_root/");
		return;
	}

	if($page)
		$article->set_limit((($tp - $page + 1) * $art_per_page - $art_align).",$art_per_page");
	else
		$article->set_limit($art_per_page);
	$articles = $article->load($art_id, $_pointer['_data_']['mod_id']);
} else {
	$articles = array();
}

// no chekojam vai registreeta *sadalja/raksts*
/*
if(
	!user_loged() &&
	($_pointer['_data_']['registrated'] == MOD_TYPE_REGISTRATED ||
	$_pointer['_data_']['module_type'] == MOD_TYPE_REGISTRATED)
)
	$tmpl = 'tmpl.registrated.php';
else
	$tmpl = 'tmpl.article.php';
*/
# Comments
if($art_id)
{
	$_SESSION['comments']['viewed'][$art_id] = $articles[0]['art_comment_count'];
	$CC = new CommentConnect('article_'.$sys_lang);
	$CC->setDb($db);
	$comments = $CC->get(array(
		'cc_table_id'=>$art_id
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
		$template->set_var('page', "$module_root/page/$tp/", 'BLOCK_article_page_prev');
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

	$path = array('jaunumi'=>array('module_id'=>'', 'module_name'=>'JAUNUMI'));
}

if($page && ($page <= $tp))
	$art_title .= sprintf(" %d. lapa ", $page);

$template->set_title($art_title);

//if($tmpl != 'tmpl.registrated.php')
/*
if($art_id) {
	if(user_loged() || $articles['art_type'] == ARTICLE_TYPE_OPEN)
	{
		if($hl)
			hl($articles['art_data'], $hl);

		$item = $articles;
		include('../modules/inc.comment_actions.php');
	}
} else
*/
$tidy_config = array(
	'doctype' => 'strict',
	'clean' => true,
	'output-xhtml' => true,
	'show-body-only' => true,
	'wrap' => 0,
	'alt-text' => ''
	);

if(count($articles))
{
	$module = new Module;
	$template->enable('BLOCK_article');
	//$template->set_ad();
	$c = 0;
	foreach($articles as $item)
	{
		++$c;

		$tidy = tidy_parse_string($item['art_data'], $tidy_config, 'UTF8');
		$tidy->cleanRepair();
		//$root = tidy_get_root($tidy);
		//printr($root);
		//die;
		$item['art_data'] = $tidy;

		$item['art_date'] = date('d.m.Y', strtotime($item['art_entered']));
		if($art_id)
		{
			$patt = '/(.*)(<hr\s+id="editor_splitter" \/>)(.*)/ims';
			$item['art_data'] = preg_replace($patt, '<div style="font-weight: bold;">\1</div><hr/>\3', $item['art_data'], 1);
			$item['art_date_f'] = proc_date($item['art_entered']);
			$template->enable('BLOCK_art_data_formatted');
		} else {
			$patt = '/<hr\s+id="editor_splitter" \/>.*/ims';
			if(preg_match($patt, $item['art_data']))
			{
				$item['art_data'] = preg_replace($patt, '', $item['art_data'], 1);
				$template->enable('BLOCK_art_cont');
			} else {
				$template->disable('BLOCK_art_cont');
			}
		}

		if($item['art_comments'] == ARTICLE_NOCOMMENTS)
			$template->disable('BLOCK_is_comments');
		else
			$template->enable('BLOCK_is_comments');

		$template->set_array($item, 'BLOCK_article');
		$template->set_var('art_path', $module->get_path($item['art_modid']), 'BLOCK_article');
		$template->parse_block('BLOCK_article', TMPL_APPEND);
	}
	$article->set_comment_count($template, $articles);
} else {
	//if($tmpl == 'tmpl.article.php')
	$template->enable('BLOCK_noarticle');
}

$template->set_right();
$template->set_login();
$template->set_reviews(13);
$template->set_poll();
$template->set_search();
$template->set_online();

$template->out();

