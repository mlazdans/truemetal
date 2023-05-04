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
require_once('lib/Logins.php');
require_once('lib/Template.php');

class MainModule extends Template
{

	var $module_name;
	var $title;

	function __construct($module_name = '', $str_main_file = 'index.tpl')
	{
		parent::__construct($GLOBALS['sys_template_root']);

		$this->set_module_name($module_name ? $module_name : get_class($this));
		$this->set_file('FILE_index', $str_main_file);

		$this->init();

		return true;
	} // MainModule

	function init()
	{
		$this->set_var('encoding', $GLOBALS['sys_encoding']);
		$this->set_var('module_root', '/'.$this->module_name);
		$this->set_var('script_version', $GLOBALS['sys_script_version']);
		$this->set_var('disable_youtube', (empty($_SESSION['login']['l_disable_youtube']) ? 0 : 1));
		$this->set_var('i_am_admin', $GLOBALS['i_am_admin']);
		$this->set_descr("Metāls Latvijā");
		$this->set_banner_top();

		return true;
	} // init

	function set_title($str_title)
	{
		$this->title = $str_title;
		$this->set_var('title', addslashes($this->title), 'FILE_index');
	} // set_title

	function get_title()
	{
		return $this->title;
	} // get_title

	function set_descr($descr)
	{
		$this->set_var("meta_descr", parse_form_data(trim($descr)));
	} // set_descr

	function set_module_name($module_name)
	{
		$this->module_name = $module_name;
	} // set_module_name

	function out()
	{
		global $sys_use_cdn, $sys_cdn_func, $sys_domain, $i_am_admin, $sys_start_time;

		if($sys_use_cdn && function_exists($sys_cdn_func))
		{
			$dom = new DOMDocument('1.0', 'utf-8');
			@$dom->loadHTML($this->parse_block('FILE_index'));
			$xdom = simplexml_import_dom($dom);

			# Images
			if($els = $xdom->xpath("//img|//script"))
			{
				foreach($els as $item)
				{
					$parts = parse_url($item->attributes()->src);

					# Outsite site
					if(!empty($parts['host']) && ($parts['host'] != $sys_domain))
						continue;

					$parts['host'] = $sys_cdn_func($parts['path']);

					//print http_build_url(false, $parts)."!<br>";
					//$item->attributes()->src = http_build_url(false, $parts);
					/*
					if(empty($host) || ($host == $sys_domain))
						$item->attributes()->src = 'http://'.$sys_cdn_func($src).$src;
					*/
				}
			}

			# Style
			if($els = $xdom->xpath("//link[@type=\"text/css\"]"))
			{
				foreach($els as $item)
				{
					$src = parse_url($item->attributes()->href, PHP_URL_PATH);
					$host = parse_url($item->attributes()->src, PHP_URL_HOST);
					if(empty($host) || ($host == $sys_domain)){
						$item->attributes()->{"href"} = 'http://'.$sys_cdn_func($src).$src;
					}
				}
			}
			print $dom->saveHTML();
		} else {
			$sys_end_time = microtime(true);
			$rendered = 'Rendered in: '.number_format(($sys_end_time - $sys_start_time), 4, '.', '').' sec';
			if($i_am_admin)
			{
				$finished = "<div>$rendered</div>";
			} else {
				$finished = "<!-- $rendered -->";
			}
			$this->set_var('tmpl_finished', $finished);
			print $this->parse_block('FILE_index');
		}
	} // out

	// function set_modules(&$modules)
	// {
	// 	foreach($modules as $module)
	// 	{
	// 		$item = &$module['_data_'];
	// 		if($item['module_visible'] == MOD_VISIBLE && $item['module_active'] == MOD_ACTIVE)
	// 		{
	// 			$this->set_var('mod_id', $item['mod_id']);
	// 			$this->set_var('module_id', $item['module_id']);
	// 			$this->set_var('module_name', toupper($item['module_name']));
	// 			$this->parse_block('BLOCK_cat', TMPL_APPEND);
	// 		}
	// 	} // while
	// } // set_modules

	// function set_submodules(&$modules, $p = '', $d = 0)
	// {
	// 	if(!$modules)
	// 		return;

	// 	$this->enable('BLOCK_subcat');
	// 	foreach($modules as $module) {
	// 		$item = &$module['_data_'];
	// 		if($item['module_visible'] == MOD_VISIBLE && $item['module_active'] == MOD_ACTIVE) {
	// 			$this->set_var('submodule_path', $p.$item['module_id']);
	// 			$this->set_var('submodule_name', ($d ? $item['module_name'] : toupper($item['module_name'])));
	// 			$this->set_var('submodule_modid', $item['mod_id']);
	// 			if(!$d)
	// 				$this->set_var('subcat_class', 'subcat');
	// 			else
	// 				$this->set_var('subcat_class', 'subcat2');
	// 			$this->parse_block('BLOCK_subcat', TMPL_APPEND);
	// 			if(!$d) // ja pirmais apaksliimenis
	// 				$this->set_submodules($module, $item['module_id'].'/', $d + 1);
	// 		}
	// 	} // while
	// } // set_submodules

	function set_right()
	{
		$this->set_file('FILE_right', 'right.tpl');
		$this->copy_block('BLOCK_right', 'FILE_right');

		$this->enable('BLOCK_right');
	} // set_right

	function set_right_defaults()
	{
		$this->set_right();
		$this->set_events();
		$this->set_recent_forum();
		$this->set_online();
		$this->set_login();
		$this->set_search();
		$this->set_jubilars();
		$this->set_recent_comments();
		$this->set_recent_reviews();
	} // set_right

	function set_poll()
	{
		$poll = new Poll;
		$poll->set_poll($this);
	} // set_poll

	function set_login()
	{
		$this->set_file('FILE_login_form', 'right/login_form.tpl');

		if(user_loged())
		{
			$this->enable('BLOCK_login_data');
			$this->set_var('login_nick', $_SESSION['login']['l_nick'], 'BLOCK_login_data');
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
		$login = new Logins;
		$this->set_file('FILE_online', 'right/online.tpl');

		$block = user_loged() ? 'BLOCK_online_item' : 'BLOCK_online_item_notloged';
		$user_count = 0;

		if($active_sessions = $login->get_active())
			$this->enable($block);

		foreach($active_sessions as $data)
		{
			$this->set_var('online_name', $data['l_nick'], 'FILE_online');
			$this->set_var('online_login_id', $data['l_login'], 'FILE_online');
			$this->parse_block($block, TMPL_APPEND);
		}

		$this->set_var('online_total', count($active_sessions));

		$this->parse_block('FILE_online');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_online'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_online

	function set_recent_reviews($limit = 4)
	{
		global $module_tree;

		$article = new Article;

		$data = $article->load(array(
			'art_modid'=>$module_tree['reviews']['_data_']['mod_id'],
			'limit'=>$limit,
			'order'=>'art_entered DESC',
			));

		if(empty($data))
			return;

		$this->set_file('FILE_r_review', 'right/review_recent.tpl');
		foreach($data as $item)
		{
			$this->{(Article::hasNewComments($item) ? "enable" : "disable")}('BLOCK_review_r_comments_new');

			$this->set_var('review_r_name', $item['art_name'], 'BLOCK_review_r_items');
			$this->set_var('review_r_comment_count', $item['res_comment_count'], 'BLOCK_review_r_items');
			$this->set_var('review_r_path', "reviews/{$item['art_id']}-".urlize($item['art_name']), 'BLOCK_review_r_items');
			$this->parse_block('BLOCK_review_r_items', TMPL_APPEND);
		}

		$this->parse_block('FILE_r_review');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_r_review'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_recent_reviews

	function set_recent_comments($limit = 10)
	{
		$Article = new Article;

		$data = $Article->load(array(
			'order'=>'res_comment_lastdate DESC',
			'limit'=>$limit,
			));

		$this->set_file('FILE_r_comment', 'right/comment_recent.tpl');
		foreach($data as $item)
		{
			$this->{(Article::hasNewComments($item) ? "enable" : "disable")}('BLOCK_comment_r_comments_new');

			$this->set_var('comment_r_name', $item['art_name'], 'BLOCK_comment_r_items');
			$this->set_var('comment_r_comment_count', $item['res_comment_count'], 'BLOCK_comment_r_items');
			$this->set_var('comment_r_path', "{$item['module_id']}/{$item['art_id']}-".urlize($item['art_name']), 'BLOCK_comment_r_items');
			$this->parse_block('BLOCK_comment_r_items', TMPL_APPEND);
		}

		$this->enable('BLOCK_comment_r_more');
		$this->parse_block('FILE_r_comment');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_r_comment'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_recent_comments

	function set_profile($login)
	{
		global $sys_user_root;

		$login['l_forumsort_themes'] = isset($login['l_forumsort_themes']) ? $login['l_forumsort_themes'] : Forum::SORT_LASTCOMMENT;
		$login['l_forumsort_msg'] = isset($login['l_forumsort_msg']) ? $login['l_forumsort_msg'] : Forum::SORT_ASC;
		$pic_localpath = $sys_user_root.'/pic/'.$login['l_id'].'.jpg';
		$tpic_localpath = $sys_user_root.'/pic/thumb/'.$login['l_id'].'.jpg';
		$pic_path = "/user/image/$login[l_login]/";
		$tpic_path = "/user/thumb/$login[l_login]/";

		$this->enable('BLOCK_profile');
		// $this->set_array($login, 'BLOCK_profile');
		$this->set_var('l_login', $login['l_login'], 'BLOCK_profile');
		$this->set_var('l_nick', $login['l_nick'], 'BLOCK_profile');
		$this->set_var('l_email', $login['l_email'], 'BLOCK_profile');

		$this->set_var('l_forumsort_themes_'.$login['l_forumsort_themes'], ' checked="checked"', 'BLOCK_profile');
		$this->set_var('l_forumsort_msg_'.$login['l_forumsort_msg'], ' checked="checked"', 'BLOCK_profile');

		if(!empty($login['l_disable_youtube']))
		{
			$this->set_var('l_disable_youtube_checked', ' checked="checked"', 'BLOCK_profile');
		} else {
			$this->set_var('l_disable_youtube_checked', '', 'BLOCK_profile');
		}

		if($login['l_emailvisible'] != Logins::EMAIL_VISIBLE)
		{
			$this->set_var('l_emailvisible', '', 'BLOCK_profile');
		} else {
			$this->set_var('l_emailvisible', ' checked="checked"', 'BLOCK_profile');
		}

		if(file_exists($pic_localpath) && file_exists($tpic_localpath))
		{
			$this->set_var('pic_path', $tpic_path, 'BLOCK_profile');
			if($info = getimagesize($pic_localpath))
			{
				$this->set_var('pic_w', $info[0], 'BLOCK_profile');
				$this->set_var('pic_h', $info[1], 'BLOCK_profile');
			} else {
				$this->set_var('pic_w', 400, 'BLOCK_profile');
				$this->set_var('pic_h', 400, 'BLOCK_profile');
			}

			$this->enable('BLOCK_picture');
		} else {
			$this->enable('BLOCK_nopicture');
		}

		$this->set_var('l_entered_f', strftime('%e. %b %Y', strtotime($login['l_entered'])), 'BLOCK_profile');
		$this->set_var('l_lastaccess_f', strftime('%e. %b %Y', strtotime($login['l_lastaccess'])), 'BLOCK_profile');
		$days = floor((time() - strtotime($login['l_lastaccess'])) / (3600 * 24));
		if($days)
		{
			if($days < 365)
			{
				$days_lv = "dienām";
				if($days % 10 == 1)
					$days_lv = "dienas";
				$this->set_var('l_lastaccess_days', " (pirms $days $days_lv)", 'BLOCK_profile');
			}
		} else {
			$this->set_var('l_lastaccess_days', " (šodien)", 'BLOCK_profile');
		}
	} // set_profile

	function set_banner_top()
	{
		if(empty($GLOBALS['top_banners']))
		{
			if($this->block_exists('BLOCK_banner_top'))
				$this->disable('BLOCK_banner_top');

			return;
		}

		$banners = $GLOBALS['top_banners'];

		$ban_id = mt_rand(0, count($banners) - 1);
		$banner = $banners[$ban_id];
		$this->set_var('banner_img', $banner['img']);
		$this->set_var('banner_alt', $banner['alt']);
		$this->set_var('banner_href', $banner['href']);

		if(isset($banner['width']))
			$this->set_var('banner_width', $banner['width']);
		else
			$this->set_var('banner_width', 170);

		if(isset($banner['height']))
			$this->set_var('banner_height', $banner['height']);
		else
			$this->set_var('banner_height', 113);
	} // set_banner_top

	function set_jubilars()
	{
		$Logins = new Logins();
		$jubs = $Logins->load(array(
			'jubilars'=>true,
			));

		$block = user_loged() ? 'BLOCK_jub_item' : 'BLOCK_jub_item_notloged';

		$this->set_file('FILE_jub', 'right/jub.tpl');

		if($jubs) {
			$this->enable($block);
		}

		foreach($jubs as $data)
		{
			$age = round($data['age'] / 365);

			$jub_year = '';
			if($age == 0){
				$jub_year = 'jauniņais';
			} elseif($age == 1){
				$jub_year = ' gadiņš';
			} else {
				if((substr($age, -2) != 11) && ($age % 10 == 1)){
					$jub_year = ' gads';
				} else {
					$jub_year = ' gadi';
				}
			}

			$this->set_var('jub_name', $data['l_nick'], 'FILE_jub');
			$this->set_var('jub_login_id', $data['l_login'], 'FILE_jub');

			if($age){
				$this->set_var('jub_info', " ($age $jub_year)", 'FILE_jub');
			} else {
				$this->set_var('jub_info', " ($jub_year)", 'FILE_jub');
			}
			$this->parse_block($block, TMPL_APPEND);
		}

		$this->parse_block('FILE_jub');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_jub'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_jubilars

	function set_events()
	{
		$forum = new Forum;
		$data = $forum->load(array(
			"fields"=>array('forum_id', 'forum_name', 'event_startdate', 'f.res_id'),
			"actual_events"=>true,
			"order"=>'event_startdate',
			));

		if(!$data)
			return;

		$this->set_file('FILE_events', 'right/events.tpl');
		$timeout = 7*24*60*60;
		$c = 0;
		$tc = count($data);
		foreach($data as $item){
			$ts = strtotime($item['event_startdate']);
			$D = date('j', $ts);
			$Dw = date('w', $ts);
			$M = date('m', $ts);

			$diff = $ts - time();

			$this->set_var('event_class', "", 'BLOCK_events_list');
			$this->set_var('event_url', Forum::Route($item), 'BLOCK_events_list');
			$this->set_var('event_title', ent($D.". ".get_month($M - 1).", ".get_day($Dw - 0)), 'BLOCK_events_list');
			//$this->set_var('event_name', ent($item['forum_name']), 'BLOCK_events_list');
			$this->set_var('event_name', $item['forum_name'], 'BLOCK_events_list');

			if($diff<$timeout){
				$this->set_var('event_class', " actual", 'BLOCK_events_list');
			}

			if($tc > 5){
				if($c==5){
					$this->enable('BLOCK_more_events_start');
					$this->enable('BLOCK_more_events_end');
				} else {
					$this->disable('BLOCK_more_events_start');
				}
			}
			$this->parse_block('BLOCK_events_list', TMPL_APPEND);
			$c++;
		}

		$this->parse_block('FILE_events');
		$this->set_var('right_item_data', $this->get_parsed_content('FILE_events'), 'BLOCK_right_item');
		$this->parse_block('BLOCK_right_item', TMPL_APPEND);
	} // set_events
} // MainModule

