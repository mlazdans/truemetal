<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

// pats pats...

require_once('lib/Module.php');
require_once('lib/Article.php');
require_once('lib/Poll.php');
require_once('lib/Calendar.php');
require_once('lib/Logins.php');
require_once('lib/Template.php');

class MainModule extends Template
{

	var $module_name;
	var $title;

	function __construct($template_root, $module_name = '',
		$str_main_file = 'index.tpl', $str_undefined = 'remove')
	{
		parent::Template($template_root, $str_undefined);

		$this->set_module_name($module_name ? $module_name : get_class($this));

		//$this->set_root($template_root);
		//$this->set_undefined($str_undefined);

		/* ielaadeejam failus */
		/* galveno failu un vidus failus, kuram jaasaucas <module_name>.tpl */
		$this->set_file('FILE_index', $str_main_file);

		$this->init();

		return true;
	} // MainModule

	function init()
	{
		global $sys_modules, $sys_encoding;

		$this->set_global('encoding', $GLOBALS['sys_encoding'], 'FILE_index', true);
		$this->set_global('http_root', $GLOBALS['sys_http_root']);
		$this->set_global('module_root', $GLOBALS['sys_http_root'].'/'.$this->module_name);
		$this->set_global('script_version', $GLOBALS['sys_script_version']);
		$this->set_descr("Metāls Latvijā");

		$this->set_banner_top();

		return true;
	} // init

	function set_title($str_title)
	{
		$this->title = $str_title;
		$this->set_global('title', addslashes($this->title), 'FILE_index', true);
	} // set_title

	function set_descr($descr)
	{
		global $i_am_admin;

		$descr = preg_replace("/(\r\n)/", "\n", $descr);

		//if($i_am_admin)
		{
			$dd = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
</head>
<body>'.
$descr.
'</body>'.
'</html>';
			$doc = new DOMDocument();
			$doc->loadHTML($dd);
			$xml = simplexml_import_dom($doc);

			$els = $xml->xpath("//a");
			//if($i_am_admin)
				//printr($els);
			foreach($els as $el)
			{
				if(isset($el[0]))
					$el = $el[0];
				//unset($el[0]);
			}

			$els = $xml->xpath("//br");
			foreach($els as $el)
				$el[0] = "\n";

			$descr = $xml->asXML();
		}

		$descr = strip_tags($descr);
		$descr = preg_replace("/[\s\s+]/", " ", $descr);
		$descr = preg_replace("/\s+/", " ", $descr);

		$parts = preg_split("/([\.\?\!])/U", $descr, -1, PREG_SPLIT_DELIM_CAPTURE);
		$meta_descr = '';
		foreach($parts as $p)
		{
			$meta_descr .= $p;
			if(mb_strlen($meta_descr) >= 250)
				break;
		}

		if(mb_strlen($meta_descr) >= 250)
		{
			$parts = preg_split("/(\s)/U", $meta_descr, -1, PREG_SPLIT_DELIM_CAPTURE);
			$meta_descr = '';
			foreach($parts as $p)
			{
				$meta_descr .= $p;
				if(mb_strlen($meta_descr) >= 250)
					break;
			}
		}

		if($meta_descr = parse_form_data(trim($meta_descr)))
			$this->set_global("meta_descr", $meta_descr);
	} // set_descr

	function set_module_name($module_name)
	{
		$this->module_name = $module_name;
	} // set_module_name

	function out()
	{
		global $sys_http_root, $sys_use_cdn, $sys_cdn_func;

		//print $this->parse_file('FILE_index');
		if($sys_use_cdn && function_exists($sys_cdn_func))
		{
			$dom = new DOMDocument('1.0', 'utf-8');
			@$dom->loadHTML($this->parse_file('FILE_index'));
			$xdom = simplexml_import_dom($dom);

			# Images
			if($els = $xdom->xpath("//img|//script"))
			{
				foreach($els as $item)
				{
					$src = parse_url($item->attributes()->src, PHP_URL_PATH);
					$item->attributes()->src = 'http://'.$sys_cdn_func($src).$src;
				}
			}

			# Style
			if($els = $xdom->xpath("//link[@type=\"text/css\"]"))
			{
				foreach($els as $item)
				{
					$src = parse_url($item->attributes()->href, PHP_URL_PATH);
					$item->attributes()->href = 'http://'.$sys_cdn_func($src).$src;
				}
			}
			print $dom->saveHTML();
		} else {
			print $this->parse_file('FILE_index');
		}
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

	function set_right($file = 'right.tpl', $img = '')
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
		$this->set_file('FILE_login_form', 'right/login_form.tpl');
		$this->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_login_form');

		if(user_loged())
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
		$this->set_file('FILE_search_form', 'right/search_form.tpl');
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
		$this->set_file('FILE_online', 'right/online.tpl');
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
			$this->set_file('FILE_r_review', 'right/review_recent.tpl');
			$this->set_var('http_root', $GLOBALS['sys_http_root'], 'FILE_r_review');
			foreach($data as $item)
			{
				$this->{(Article::hasNewComments($item) ? "enable" : "disable")}('BLOCK_review_r_comments_new');

				$this->set_var('review_r_name', $item['art_name'], 'BLOCK_review_r_items');
				$this->set_var('review_r_comment_count', $item['art_comment_count'], 'BLOCK_review_r_items');
				$this->set_var('review_r_path', "article/".$item['art_id'], 'BLOCK_review_r_items');
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

