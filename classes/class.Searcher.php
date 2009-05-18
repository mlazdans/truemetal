<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('../classes/class.Article.php');
require_once('../classes/class.Module.php');
require_once('../classes/class.Forum.php');

class Searcher
{
	function Searcher()
	{
	}

	function m_count(&$modules)
	{
		$c = 0;
		if(isset($modules['_data_']) && $modules['_data_']['score'])
			$c = 1;

		if(is_array($modules))
			foreach($modules as $module)
				$c += $this->m_count($module);

		return $c;
	}

	function a_count(&$articles)
	{
		$c = 0;
		if(is_array($articles))
			foreach($articles as $article)
				if($article['score'])
					$c += 1;

		return $c;
	}

	function search($q)
	{
		//$q = preg_quote(preg_quote($q));
		//$q = preg_replace('/ /', '*', $q).'';
		//$q = '('.preg_replace('/\s/', '|', $q).')';
		$q = parse_search_q($q);

		$article = new Article;
		$module = new Module;
		$forum = new Forum;

		//$data['modules'] = $module->search($q);
		$data['articles'] = $article->search($q);
		$data['forum'] = $forum->search($q);

		//$data['module_count'] = $this->m_count($data['modules']);
		$data['article_count'] = $this->a_count($data['articles']);
		$data['forum_count'] = count($data['forum']);

		return $data;
	}

} // Searcher
