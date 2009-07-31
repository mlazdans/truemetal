<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

// pats pats...

require_once('../classes/class.Module.php');
require_once('../classes/class.Article.php');
require_once('../classes/class.Poll.php');
require_once('../classes/class.Calendar.php');
require_once('../classes/class.Logins.php');
require_once('../classes/class.Template.php');

class MainModule extends Template
{

	var $module_name;
	var $title;

	function MainModule($template_root, $module_name = '',
		$str_main_file = 'tmpl.index.php', $str_undefined = 'remove')
	{
		parent::Template($template_root, $str_undefined);

		$this->set_module_name($module_name ? $module_name : get_class($this));

		//$this->set_root($template_root);
		//$this->set_undefined($str_undefined);

		/* ielaadeejam failus */
		/* galveno failu un vidus failus, kuram jaasaucas tmpl.<module_name>.php */
		$this->set_file('FILE_index', $str_main_file);

		$this->init();

		return true;
	} // MainModule

	function init()
	{
		global $sys_modules, $sys_lang_def, $sys_lang, $sys_encoding;

		$this->set_global('encoding', $GLOBALS['sys_encoding'], 'FILE_index', true);
		$this->set_global('http_root', $GLOBALS['sys_http_root']);
		$this->set_global('module_root', $GLOBALS['sys_http_root'].'/'.$this->module_name);
		//$this->set_global('now', $GLOBALS['now']);
		$this->set_global('sys_lang', $sys_lang);
		$this->set_global('script_version', $GLOBALS['sys_script_version']);

		$this->set_banner_top();
		//$this->set_array($sys_lang_def);

		return true;
	} // init

	function set_title($str_title)
	{
		$this->title = $str_title;
		$this->set_global('title', $this->title, 'FILE_index', true);
	} // set_title

	function set_module_name($module_name)
	{
		$this->module_name = $module_name;
	} // set_module_name

	function out()
	{
		global $i_am_admin, $sys_http_root;

		print $this->parse_file('FILE_index');
		/*
		if($i_am_admin)
		{
			$dom = new DOMDocument('1.0', 'utf-8');
			@$dom->loadHTML($this->parse_file('FILE_index'));
			//$dom->normalizeDocument();
			$xdom = simplexml_import_dom($dom);

			if($els = $xdom->xpath("//img"))
			{
				foreach($els as $item)
				{
					$src = parse_url($item->attributes()->src, PHP_URL_PATH);
					$item->attributes()->src = 'http://'.cdn_domain($src).$src;
				}
			}
			print $dom->saveHTML();
		} else {
			print $this->parse_file('FILE_index');
		}
		*/
		//$content = $this->parse_file('FILE_index');
		//$variable_pattern = '[a-zA-z0-9_^}]{1,}';
		//print preg_replace('/{'.$variable_pattern.'}/U', '', $content);
	} // out

	function set_modules(&$modules)
	{
		foreach($modules as $module)
		{
			$item = &$module['_data_'];
			if($item['module_visible'] == MOD_VISIBLE && $item['module_active'] == MOD_ACTIVE)
			{
				$this->set_var('mod_id', $item['mod_id']);
				$this->set_var('module_id', $item['module_id']);
				$this->set_var('module_name', toupper($item['module_name']));
				$this->parse_block('BLOCK_cat', TMPL_APPEND);
			}
		} // while
	} // set_modules

	function set_submodules(&$modules, $p = '', $d = 0)
	{
		if(!$modules)
			return;

		$this->enable('BLOCK_subcat');
		foreach($modules as $module) {
			$item = &$module['_data_'];
			if($item['module_visible'] == MOD_VISIBLE && $item['module_active'] == MOD_ACTIVE) {
				$this->set_var('submodule_path', $p.$item['module_id']);
				$this->set_var('submodule_name', ($d ? $item['module_name'] : toupper($item['module_name'])));
				$this->set_var('submodule_modid', $item['mod_id']);
				if(!$d)
					$this->set_var('subcat_class', 'subcat');
				else
					$this->set_var('subcat_class', 'subcat2');
				$this->parse_block('BLOCK_subcat', TMPL_APPEND);
				if(!$d) // ja pirmais apaksliimenis
					$this->set_submodules($module, $item['module_id'].'/', $d + 1);
			}
		} // while
	} // set_submodules

	function set_right($file = 'tmpl.right.php', $img = '')
	{
		$this->set_file('FILE_right', $file);
		$this->copy_block('BLOCK_right', 'FILE_right');

		$this->enable('BLOCK_right');
	} // set_right

	function set_label($path)
	{
		if($c = count($path))
		{
			$this->enable('BLOCK_label');
			if($c > 3)
				$path = array_slice($path, -3);
		}

		$p = $GLOBALS['sys_http_root'].'/';
		foreach($path as $k=>$label) {
			if($label['module_id'])
				$p .= $label['module_id'].'/';
			if(mb_strlen($label['module_name']) > 20)
				$label['module_name'] = mb_substr($label['module_name'], 0, 18).'..';
			$this->set_var('label_name', $label['module_name']);
			$this->set_var('label_path', $p);
			$this->parse_block('BLOCK_label', TMPL_APPEND);
		}
	}

	function set_calendar($y = 0, $m = 0, $d = 0)
	{
		$calendar = new Calendar;
		$calendar->set_calendar($this, $y, $m, $d);
	} // set_calendar

	function set_poll()
	{
		$poll = new Poll;
		$poll->set_poll($this);
	} // set_poll

	function set_login()
	{
		$this->set_file('FILE_login_form', 'tmpl.login_form.php');
		$this->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_login_form');

		if(isset($_SESSION['login']['l_id']) && $_SESSION['login']['l_id'])
		{
			$this->enable('BLOCK_login_data');
			$this->set_var('login_nick', $_SESSION['login']['l_nick'], 'BLOCK_login_data');
			//$this->set_var('login_firstname', $_SESSION['login']['l_firstname']);
			//$this->set_var('login_lastname', $_SESSION['login']['l_lastname']);
		} else {
			$this->enable('BLOCK_login_form');
			$referer = $_SERVER["REQUEST_URI"];
			if(!empty($_SERVER["QUERY_STRING"]))
				$referer .= "?".$_SERVER["QUERY_STRING"];
			$this->set_var("referer", urlencode($referer), 'BLOCK_login_form');
		}

		$this->parse_block('FILE_login_form');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_login_form'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_login

	function set_search($search_q = '')
	{
		$this->set_file('FILE_search_form', 'tmpl.search_form.php');
		$this->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_search_form');
		$this->set_var('search_q', $search_q, 'FILE_search_form');

		$this->parse_block('FILE_search_form');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_search_form'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_search

	function set_recent_forum()
	{
		$forum = new Forum;
		$forum->set_recent_forum($this);
	} // set_recent_forum

	function set_online()
	{
		global $db, $sys_http_root;

		$login = new Logins;
		$this->set_file('FILE_online', 'tmpl.online.php');
		$this->set_var('http_root', $sys_http_root, 'FILE_online');

		$block = user_loged() ? 'BLOCK_online_item' : 'BLOCK_online_item_notloged';
		$user_count = 0;
		$parsed_users = array();

		$active_sessions = $login->get_active();

		if($active_sessions)
		{
			$this->enable($block);
		}

		foreach($active_sessions as $data)
		{
			if(!in_array($data['l_nick'], $parsed_users))
			{
				++$user_count;
				$this->set_var('online_name', $data['l_nick'], 'FILE_online');
				$this->set_var('online_login_id', $data['l_login'], 'FILE_online');
				$this->parse_block($block, TMPL_APPEND);
				$parsed_users[] = $data['l_nick'];
			}
		}

		$this->set_var('online_total', $user_count);

		$this->parse_block('FILE_online');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_online'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_online

	function set_reviews($limit = 4)
	{
		global $module_tree;

		$article = new Article;
		//$comment = new Comment('article_comments', 'art_id');

		$data = $article->load(array(
			'art_modid'=>$module_tree['reviews']['_data_']['mod_id'],
			'limit'=>$limit,
			'order'=>'art_entered DESC',
			));

		if(count($data))
		{
			$this->set_file('FILE_r_review', 'tmpl.review_recent.php');
			$this->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_r_review');
			foreach($data as $item)
			{
				$this->set_var('review_r_name', $item['art_name'], 'BLOCK_review_r_items');
				//$this->set_var('review_r_comment_count', $article->comment_count($item['art_id']), 'BLOCK_review_r_items');
				$this->set_var('review_r_comment_count', $item['art_comment_count'], 'BLOCK_review_r_items');
				$this->set_var('review_r_path', "article/".$item['art_id'], 'BLOCK_review_r_items');
				//$comment->show_comments($this, $item['art_id']);
				$this->parse_block('BLOCK_review_r_items', TMPL_APPEND);
			}

			$this->parse_block('FILE_r_review');
			$this->set_var('right_item_data', $this->get_parsed_content('FILE_r_review'), 'BLOCK_right_item');
			$this->parse_block('BLOCK_right_item', TMPL_APPEND);
		}
	} // set_reviews

	function set_profile($login_data)
	{
		$logins = new Logins;
		$logins->set_profile($this, $login_data);
	} // set_profile
/*
	function set_cache($module_id, $key, &$data, $compress = false, $expire = 3600)
	{
		global $_CACHE, $db, $sys_use_chache;

		if(!$sys_use_chache)
		{
			return false;
		}

		if($_CACHE->set($key, $data, $compress, $expire))
		{
			update_cache_item($key);
			return true;
		} else {
			return false;
		}
	} // set_cache

	function get_cache($module_id, $key)
	{
		global $_CACHE, $db, $sys_use_chache;

		if(!$sys_use_chache)
		{
			return false;
		}

		if($item_date = $db->ExecuteSingle("SELECT * FROM cache_items WHERE item_id = '$key'"))
		{
			if($module_date = $db->ExecuteSingle("SELECT * FROM cache_modules WHERE module_id = '$module_id'"))
			{
				if($module_date['last_update'] > $item_date['last_update'])
				{
					return false;
				}
			}
		}

		return $_CACHE->get($key);
	} // get_cache
*/
	function set_banner_top()
	{
		$banners = array(
		/*
			array(
				'img'=>'banner_tabestic_enteron_depo2005-11-10.gif',
				'alt'=>'TABESTIC ENTERON klubā Depo',
				'href'=>'/article/253/',
			),
			array(
				'img'=>'banner_black_friday2006.gif',
				'alt'=>'MELNĀ PIEKTDIENA 2006',
				'href'=>'/article/278/',
			),
			array(
				'img'=>'banner_skyforger2006-02-11.jpg',
				'alt'=>'SKYFORGER klubā Salamandra',
				'href'=>'/article/285/',
			),
			array(
				'img'=>'banner_udo2006riga.jpg',
				'alt'=>'U.D.O. Rīgā!',
				'href'=>'/article/286/',
			),
			array(
				'img'=>'banner_krisiun2006.gif',
				'alt'=>'KRISIUN Rīgā',
				'href'=>'/article/288/',
			),
			array(
				'img'=>'banner_metalmania2006.gif',
				'alt'=>'METALMANIA 2006',
				'href'=>'/article/282/',
			),
			array(
				'img'=>'banner_tvaikas2006.gif',
				'alt'=>'TVAIKAS III',
				'href'=>'/article/291/',
			),
			array(
				'img'=>'banner_nile2006.jpg',
				'alt'=>'NILE Rīgā',
				'href'=>'/article/301/',
			),
			array(
				'img'=>'banner_metalshow2006.jpg',
				'alt'=>'MetalShow.lv Open Air 2006',
				'href'=>'/article/321/',
			),
			array(
				'img'=>'banner_moonspell2006.jpg',
				'alt'=>'MOONSPELL Rīgā',
				'href'=>'/article/339/',
			),
			array(
				'img'=>'banner_melnaa_piektdiena2006-13okt.jpg',
				'alt'=>'MELNĀ PIEKTDIENA',
				'href'=>'/article/341/',
			),
			array(
				'img'=>'banner_preternatural_statical2007.gif',
				'alt'=>'PRETERNATURAL debijas albuma &quot;Statical&quot; prezentācija',
				'href'=>'/article/399/',
			),
			array(
				'img'=>'banner_antithesis2007.gif',
				'alt'=>'Antithesis: Melancholia on Earth',
				'href'=>'/article/422/',
			),
			array(
				'img'=>'banner_metal_mania2008.gif',
				'alt'=>'METALMANIA 2008',
				'href'=>'/article/455/',
			),
			array(
				'img'=>'banner_moscow_doom_festival32008.jpg',
				'alt'=>'MOSCOW DOOM FESTIVAL, Chapter III',
				'href'=>'/article/462/',
			),
			array(
				'img'=>'banner_metalshow2008.gif',
				'alt'=>'MetalShow.lv Open Air 2008',
				'href'=>'/article/480/',
			),
			array(
				'img'=>'banner_vader2008.gif',
				'alt'=>'VADER',
				'href'=>'/article/500/',
			),
			array(
				'img'=>'banner_kipelov2008.gif',
				'alt'=>'KIPELOV',
				'href'=>'/article/520/',
			),
			array(
				'img'=>'banner_enslaved2008.gif',
				'alt'=>'ENSLAVED',
				'href'=>'/article/524/',
			),
			array(
				'img'=>'banner_kotzen2008.gif',
				'alt'=>'RICHIE KOTZEN',
				'href'=>'/article/530/',
			),
			array(
				'img'=>'banner_kreator2009.gif',
				'alt'=>'KREATOR',
				'href'=>'/article/525/',
			),
			array(
				'img'=>'banner_apocalyptica_2009.gif',
				'alt'=>'APOCALYPTICA',
				'href'=>'/article/562/',
			),
			array(
				'img'=>'banner_metalshow2009.jpg',
				'alt'=>'METALSHOW 2009',
				'href'=>'/article/580/',
			),
*/
			array(
				'img'=>'banner_devilstone_open_air_2009.gif',
				'alt'=>'DEVILSTONE OPEN AIR 2009',
				'href'=>'/article/576/',
			),
		);

		$ban_id = mt_rand(0, count($banners) - 1);
		$banner =$banners[$ban_id];
		$this->set_var('banner_img', $banner['img']);
		$this->set_var('banner_alt', $banner['alt']);
		$this->set_var('banner_href', $banner['href']);
	} // set_banner_top

} // MainModule

