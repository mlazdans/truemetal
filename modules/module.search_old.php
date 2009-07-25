<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

$search_q = get('search_q');

function set_module_search(&$template, &$modules, $path = '')
{
	global $search_q;

	$template->set_var('search_q', $search_q);
	if(isset($modules['_data_'])) {
		$module = $modules['_data_'];
		if($module['score'] && $module['module_id'][0] != '_') {
			$template->set_var('search_path', $path);
			$template->set_var('search_name', $module['module_name']);
			$template->parse_block('BLOCK_searchitem', TMPL_APPEND);
		}
	}
	foreach($modules as $key=>$module)
		if($key != '_data_')
			set_module_search($template, $modules[$key], $path.'/'.$key);
} // set_module_search

function set_article_search(&$template, &$articles)
{
	global $search_q;

	$module = new Module;
	$template->set_var('search_q', $search_q, 'BLOCK_searchitem');
	foreach($articles as $key=>$article)
	{
		//$template->set_var('search_path', '/news/'.$article['art_id']);
		$template->set_var('search_path', $module->get_path($article['art_modid']).$article['art_id'], 'BLOCK_searchitem');
		$template->set_var('search_name', $article['art_name'], 'BLOCK_searchitem');
		$template->set_var('search_cat', $article['module_name'], 'BLOCK_searchitem');
		$template->parse_block('BLOCK_searchitem', TMPL_APPEND);
	}
} // set_article_search

function set_forum_search(&$template, &$forums)
{
	global $search_q;

	$template->set_var('search_q', $search_q, 'BLOCK_searchitem');
	foreach($forums as $forum)
	{
		if(!$forum['forum_name'])
		{
			continue;
		}

		$template->set_var('search_path', '/forum/'.$forum['forum_forumid'], 'BLOCK_searchitem');
		$template->set_var('search_name', $forum['forum_name'], 'BLOCK_searchitem');
		$template->set_var('search_cat', 'Forums', 'BLOCK_searchitem');
		$template->parse_block('BLOCK_searchitem', TMPL_APPEND);
	}
} // set_forum_search

function set_searches()
{
} // set_searches

require_once('../classes/class.MainModule.php');
require_once('../classes/class.Searcher.php');

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_file('FILE_search', 'tmpl.search.php');
$template->set_title($sys_lang_def['search']);
$template->set_array($sys_lang_def, 'BLOCK_middle');
$template->copy_block('BLOCK_middle', 'FILE_search');

$searcher = new Searcher;
if(strlen($search_q) < 3)
	$template->enable('BLOCK_searcherror');
else {
	$f = fopen($sys_root.'/utils/search.log', 'a');
	$login_id = isset($_SESSION['login']['l_id']) ? $_SESSION['login']['l_id'] : 0;
	fputs($f, '['.date('Y-m-d H:i:s')."] login_id=$login_id\t$_SERVER[REMOTE_ADDR]\t$search_q\n");
	fclose($f);
	$data = $searcher->search($search_q);
	if(!$data['forum_count'] && !$data['article_count'])
	//if(!$data['article_count'])
		$template->enable('BLOCK_notfound');
	else {
		//set_module_search($template, $data['modules']);
		set_article_search($template, $data['articles']);
		set_forum_search($template, $data['forum']);
		$template->enable('BLOCK_search');
	}
}
$template->set_var('search_q', htmlspecialchars($search_q));
//$template->enable('BLOCK_cat_name');
//$template->set_var('current_module_name', tolower($sys_lang_def['search'], true));

$path = array('archive'=>array('module_id'=>'search', 'module_name'=>'MEKLĒT'));

$template->set_right();
$template->set_search(htmlspecialchars($search_q));
$template->set_reviews();
$template->set_poll();
$template->set_online();

$template->out();

