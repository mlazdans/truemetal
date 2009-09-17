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
$hl = urldecode(get("hl"));
$page = ($art_id == 'page' ? (int)array_shift($sys_parameters) : 0);
$art_id = (int)$art_id;

# Template
$template = new MainModule($sys_template_root, $sys_module_id);
$template->set_file('FILE_article', 'article.tpl');
$template->copy_block('BLOCK_middle', 'FILE_article');

# Loading
$article = new Article();

$tc = 0;
if($art_id)
{
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
	$art_title .= (isset($item['art_name']) ? " - ".$item['art_name'] : "");
} elseif($_pointer['_data_']['mod_id']) {
	$cc = $db->ExecuteSingle("SELECT COUNT(*) AS cc FROM view_mainpage");
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
		//$limit = (($tp - $page) * $art_per_page - $art_align).",$art_per_page";
	else
		$limit = $art_per_page;

	$sql = "SELECT * FROM view_mainpage ORDER BY art_entered DESC LIMIT $limit";
	/*
	if($i_am_admin)
	{
		print "art_align=$art_align, tc=$tc, tp=$tp, sql=$sql";
	}
	*/
	$articles = $db->Execute($sql);

// ORDER BY art_entered DESC  LIMIT 10
/*
	$articles = array();
	$sql = "
	SELECT * FROM (
	) AS docs
	ORDER BY art_date
";*/
/*
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
		*/
} else {
	$articles = array();
}

# Comments
if($art_id && isset($articles[0]))
{
	$template->set_file('FILE_article_comments', 'comments.tpl');
	$template->copy_block('BLOCK_article_comments', 'FILE_article_comments');
	$template->enable('BLOCK_article_comments_head');

	if(user_loged())
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

			if($item['module_id'] == 'forum')
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

		if($item['module_id'] == 'forum')
		{
			$itemF = $item;
			$itemF['forum_id'] = $item['art_id'];
			$itemF['forum_comment_count'] = $item['art_comment_count'];
			$itemF['forum_lastcommentdate'] = $item['art_comment_lastdate'];
			$template->{(Forum::hasNewComments($itemF) ? "enable" : "disable")}('BLOCK_comments_new');
		} else {
			$template->{(Article::hasNewComments($item) ? "enable" : "disable")}('BLOCK_comments_new');
		}

		$template->set_array($item, 'BLOCK_article');
		//$template->set_var('art_path', $module->get_path($item['art_modid']), 'BLOCK_article');
		$template->parse_block('BLOCK_article', TMPL_APPEND);
	}
	//$article->set_comment_count($template, $articles);
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	$template->enable('BLOCK_noarticle');
}

$art_title .= ($hl ? sprintf(" - meklēšana: %s", $hl) : "");

$template->set_title($art_title);

$template->set_right();
$template->set_login();
$template->set_online();
$template->set_search();
$template->set_reviews(13);
$template->set_poll();

$template->out();

