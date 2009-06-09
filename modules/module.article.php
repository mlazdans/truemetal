<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

require_once('../classes/class.MainModule.php');
require_once('../classes/class.Article.php');
require_once('../classes/class.Module.php');

$art_per_page = 20;

# GET/POST
$art_id = array_shift($sys_parameters);
$action = isset($_POST['action']) ? $_POST['action'] : '';
$hl = urldecode(get("hl"));

if($art_id == 'page')
{
	$page = (int)array_shift($sys_parameters);
} else {
	$page = 0;
}

$art_id = (int)$art_id;

$article = new Article;
if(!$art_id)
	$article->date_format='%d.%m.%Y';

$tc = 0;
if($art_id) {
	$articles = $article->load($art_id);
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

# ++ cache
/*
$cache_id = $sys_module_id;
if(!$art_id && (($data = $template->get_cache($sys_module_id, $cache_id)) !== false) && $data)
{
	$template->create_file('FILE_article', $data);
	$article->set_comment_count($template, $articles);
	$template->set_right();
	$template->set_login();
	$template->set_reviews();
	$template->set_poll();
	$template->set_search();
	$template->set_online();
	$template->set_calendar();

	$template->out();
	return;
}
*/
# -- cache

// no chekojam vai registreeta *sadalja/raksts*
if($art_id)
{
	if(
		!user_loged() &&
		($articles['art_type'] == ARTICLE_TYPE_REGISTRATED ||
		$_pointer['_data_']['registrated'] == MOD_TYPE_REGISTRATED ||
		$_pointer['_data_']['module_type'] == MOD_TYPE_REGISTRATED)
	)
		$tmpl = 'tmpl.registrated.php';
	else
		$tmpl = 'tmpl.comments.php';
} else
	if(
		!user_loged() &&
		($_pointer['_data_']['registrated'] == MOD_TYPE_REGISTRATED ||
		$_pointer['_data_']['module_type'] == MOD_TYPE_REGISTRATED)
	)
		$tmpl = 'tmpl.registrated.php';
	else
		$tmpl = 'tmpl.article.php';

// ielaadee visus rakstu zem visaam apakskategorijaam
//if(!count($articles))
//	$articles = $article->load_under($_pointer);

//$TMPL_CACHE_ID = "$sys_module_id:$tmpl";
//if(!$sys_tmpl_cache || !($template =& tmpl_cache_fetch($TMPL_CACHE_ID)))
{
	$template = new MainModule($sys_template_root, $sys_module_id);
	$template->set_file('FILE_article', $tmpl);
	$template->copy_block('BLOCK_middle', 'FILE_article');
}

# Pages
if($tc)
{
	//print ("\$tc=$tc:\$tp=$tp:\$art_align=$art_align");
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

//if($sys_tmpl_cache)
//{
//	tmpl_cache_store($TMPL_CACHE_ID, $template);
//}

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

if($tmpl != 'tmpl.registrated.php')
if($art_id) {
	if(user_loged() || $articles['art_type'] == ARTICLE_TYPE_OPEN)
	{
		if($hl)
			hl($articles['art_data'], $hl);

		$item = $articles;
		include('../modules/inc.comment_actions.php');
	}
} elseif(count($articles)) {
	$module = new Module;
	$template->enable('BLOCK_article');
	//$template->set_ad();
	$c = 0;
	foreach($articles as $item)
	{
		++$c;
		//if($c <= ARTICLE_TO_SHOW)
		//{
			// ja ir atdaliitaajs (ivads->turpinaajums)
			$patt = '/<hr\s+id=editor_splitter>.*/ims';
			if(preg_match($patt, $item['art_data']))
			{
				$item['art_data'] = preg_replace($patt, '', $item['art_data'], 1);
				$template->enable('BLOCK_art_cont');
			} else {
				$template->disable('BLOCK_art_cont');
			}
		/*
		} else {
			$item['art_date'] = '';
			$template->disable('BLOCK_art_cont');
			$template->disable('BLOCK_art_data');
		}*/

		//$item['art_date'] = proc_date($item['art_date']);
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
	if($tmpl == 'tmpl.article.php')
		$template->enable('BLOCK_noarticle');
	//$template->set_var('module_data', $_pointer['_data_']['module_data']);
}

$tmp = $_pointer2;
if(isset($tmp['_data_']))
	unset($tmp['_data_']);

if(count($tmp))
{
	$template->enable('BLOCK_subcat_top');
	$template->enable('BLOCK_subcat_bottom');
}

/*
set_parts($template);
//$template->set_label($path);
$template->set_right($sys_modules, $sys_modules);
$template->set_modules($sys_modules);
//$template->set_submodules($_pointer2);
$template->set_poll();
$template->set_login();
$template->set_calendar();
*/

$template->set_right();
$template->set_login();
$template->set_reviews(13);
$template->set_poll();
$template->set_search();
$template->set_online();
$template->set_calendar();

$template->out();

# ++ cache
/*
if(!$art_id)
{
	$template->parse_file('FILE_article');
	$template->set_cache($sys_module_id, $cache_id, $template->get_parsed_content('FILE_article'));
}
*/
# -- cache

?>
