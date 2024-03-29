<?php declare(strict_types = 1);

use dqdp\Template;

class MainModule
{
	var $module_name;
	var $title;
	var Template $TRight;
	var Template $Index;

	function __construct(string $module_name, string $main_file = 'index.tpl')
	{
		global $sys_charset, $sys_script_version, $sys_upload_http_root, $user_pic_w, $user_pic_h, $user_pic_tw, $user_pic_th;

		$this->Index = new_template($main_file);

		if($parts = explode("/", $module_name)){
			$module_name = $parts[0];
		}

		$this->Index->set_var('encoding', $sys_charset);
		$this->Index->set_var('module_id', $module_name);
		$this->Index->set_var('module_root', "/$module_name/");
		$this->Index->set_var('upload_root', $sys_upload_http_root);
		$this->Index->set_var('script_version', $sys_script_version);
		$this->Index->set_var('user_pic_w', $user_pic_w);
		$this->Index->set_var('user_pic_h', $user_pic_h);
		$this->Index->set_var('user_pic_tw', $user_pic_tw);
		$this->Index->set_var('user_pic_th', $user_pic_th);
		$this->Index->set_var('disable_youtube', empty(User::get_val('l_disable_youtube')) ? 0 : 1);

		if(User::logged()){
			$this->Index->set_with_prefix("USER_", User::data());
		}

		$this->set_descr("Metāls Latvijā");
		$this->set_banner_top();
	}

	function add_file(string $file_name): Template
	{
		return new_template($file_name)->set_array($this->Index->get_vars());
	}

	function set_title($str_title)
	{
		$this->title = $str_title;
		$this->Index->set_var('title', specialchars($this->title));

		return $this;
	}

	function get_title()
	{
		return $this->title;
	}

	function set_descr($descr)
	{
		$this->Index->set_var("meta_descr", specialchars(trim($descr)));

		return $this;
	}

	function out(null|Template|TrueResponseInterface $T)
	{
		global $sys_debug, $sys_start_time, $sys_encoding;

		if($T instanceof TrueResponseInterface){
			$T->out();
			return;
		}

		if($T instanceof Template){
			$this->Index->set_block_string($T->parse(), 'BLOCK_middle');
		}

		$sys_end_time = microtime(true);

		$mem_usage = sprintf("Mem usage: %s MB\n", number_format(memory_get_peak_usage(true)/1024/1204, 2));
		$rendered = sprintf("Rendered in: %s sec\n", number_format(($sys_end_time - $sys_start_time), 4, '.', ''));

		if(User::is_admin())
		{
			$finished = "<div><pre>$mem_usage$rendered</pre></div>";
		} else {
			$finished = "<!-- $rendered -->";
		}
		$this->Index->set_var('tmpl_finished', $finished);

		# Default json handleris atgriež middle
		if(isset($_GET['json']))
		{
			header('Content-Type: text/javascript; charset='.$sys_encoding);

			$jsonData = new StdClass;
			$jsonData->title = "[ TRUEMETAL ".$this->get_title()." ]";
			$jsonData->html = $this->Index->get_block('BLOCK_container')->parse();
			print json_encode($jsonData);
		} else {
			if(isset($this->TRight)){
				$this->Index->set_block_string($this->TRight->parse(), 'BLOCK_right');
			}
			print $this->Index->parse();
		}

		return $this;
	}

	function add_right_item(string $name, string $content) {
		if(!isset($this->TRight)){
			$this->TRight = $this->add_file('right.tpl');
		}

		$this->Index->enable('BLOCK_right');
		$this->TRight->set_var('right_item_name', $name);
		$this->TRight->set_var('right_item_data', $content);
		// $this->TRight->parse_block('BLOCK_right_item', TMPL_APPEND);
		$this->TRight->parse_block('BLOCK_right_item', TMPL_APPEND);

		return $this;
	}

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

	function set_right_defaults()
	{
		$this->set_events();
		$this->set_recent_forum();
		$this->set_online();
		$this->set_login();
		$this->set_search();
		$this->set_jubilars();
		$this->set_recent_comments();
		// $this->set_recent_reviews();
	}

	function set_login()
	{
		$TLogin_form = $this->add_file('right/login_form.tpl');

		$name = "Pieslēgties";
		if(User::logged()){
			$name = "Login";
			$TLogin_form->enable('BLOCK_login_data');
			$TLogin_form->set_var('login_nick', User::get_val('l_nick'));
		} else {
			$referer = $_SERVER["REQUEST_URI"];
			// if(!empty($_SERVER["QUERY_STRING"])){
			// 	$referer .= "?".$_SERVER["QUERY_STRING"];
			// }
			$TLogin_form->set_var("referer", specialchars($referer));
			$TLogin_form->enable('BLOCK_login_form');
		}

		return $this->add_right_item($name, $TLogin_form->parse());
	}

	function set_search($search_q = '')
	{
		$TSearch = $this->add_file('right/search_form.tpl');
		$TSearch->set_var('search_q', $search_q);

		return $this->add_right_item("Meklētājs", $TSearch->parse());
	}

	function set_recent_forum()
	{
		$F = (new ResForumFilter(forum_allow_childs: 0))
		->rows(10)
		->orderBy("COALESCE(res_comment_last_date, res_entered) DESC")
		->fields('forum_id', 'res_name', 'res_id', 'res_comment_last_date', 'res_comment_count', 'res_route', 'res_entered')
		;

		if($R = set_recent_comments((new ViewResForumEntity)->getAll($F))){
			return $this->add_right_item('Forums', $R->parse());
		}

		return $this;
	}

	function set_online()
	{
		$TLogin = $this->add_file('right/online.tpl');

		$block = User::logged() ? 'BLOCK_online_item' : 'BLOCK_online_item_notloged';

		$active_sessions = Logins::get_active();
		$online_total = count($active_sessions);

		$TLogin->enable_if($online_total > 0, $block);
		foreach($active_sessions as $data)
		{
			$TLogin->set_var('online_nick', $data->l_nick);
			$TLogin->set_var('online_hash', $data->l_hash);
			$TLogin->parse_block($block, TMPL_APPEND);
		}


		return $this->add_right_item("Online [$online_total]", $TLogin->parse());
	}

	function set_recent_comments($limit = 10)
	{
		$F = (new ResArticleFilter())->orderBy('res_comment_last_date DESC')->rows($limit);
		if($R = set_recent_comments((new ViewResArticleEntity)->getAll($F))){
			return $this->add_right_item("Komentāri", $R->parse());
		}
	}

	function set_banner_top()
	{
		if(empty($GLOBALS['top_banners']))
		{
			if($this->Index->block_exists('BLOCK_banner_top'))
				$this->Index->disable('BLOCK_banner_top');

			return;
		}

		$banners = $GLOBALS['top_banners'];

		$ban_id = mt_rand(0, count($banners) - 1);
		$banner = $banners[$ban_id];
		$this->Index->set_var('banner_img', $banner['img']);
		$this->Index->set_var('banner_alt', $banner['alt']);
		$this->Index->set_var('banner_href', $banner['href']);

		if(isset($banner['width']))
			$this->Index->set_var('banner_width', $banner['width']);
		else
			$this->Index->set_var('banner_width', 170);

		if(isset($banner['height']))
			$this->Index->set_var('banner_height', $banner['height']);
		else
			$this->Index->set_var('banner_height', 113);
	} // set_banner_top

	function set_jubilars()
	{
		$jubs = (new ViewJubilarsEntity)->getAll();

		if(!$jubs->count()){
			return $this;
		}

		$block = User::logged() ? 'BLOCK_jub_item' : 'BLOCK_jub_item_notloged';

		$TJub = $this->add_file('right/jub.tpl');
		$TJub->enable($block);

		foreach($jubs as $j)
		{
			$jub_year = '';
			if($j->age == 0){
				$jub_year = 'jauniņais';
			} elseif($j->age == 1){
				$jub_year = ' gadiņš';
			} else {
				if((substr((string)$j->age, -2) != 11) && ($j->age % 10 == 1)){
					$jub_year = ' gads';
				} else {
					$jub_year = ' gadi';
				}
			}

			$TJub->set_var('l_nick', $j->l_nick);
			$TJub->set_var('l_hash', $j->l_hash);

			if($j->age){
				$TJub->set_var('jub_info', " ($j->age $jub_year)");
			} else {
				$TJub->set_var('jub_info', " ($jub_year)");
			}
			$TJub->parse_block($block, TMPL_APPEND);
		}

		return $this->add_right_item("Jubilāri", $TJub->parse());
	}

	function set_events()
	{
		$F = (new ResForumFilter(actual_events: true))
		->orderBy('event_startdate')
		->fields('forum_id', 'res_name', 'event_startdate', 'res_id', 'res_route')
		;

		$data = (new ViewResForumEntity())->getAll($F);

		if(!$data->count()){
			return;
		}

		$TEvents = $this->add_file('right/events.tpl');

		$c = 0;
		$tc = count($data);
		foreach($data as $item){
			$ts = strtotime($item->event_startdate);
			$D = date('j', $ts);
			$Dw = date('w', $ts);
			$M = date('m', $ts);

			$diff = floor(($ts - time()) / (3600 * 24));

			$TEvents->set_var('event_class', "");
			$TEvents->set_var('event_title', specialchars($D.". ".get_month($M - 1).", ".get_day($Dw - 0)));
			$TEvents->set_var('event_name', specialchars($item->res_name));
			$TEvents->set_var('event_url', $item->res_route);

			if($diff<2){
				$TEvents->set_var('event_class', " actual0");
			} elseif($diff<4){
				$TEvents->set_var('event_class', " actual1");
			} elseif($diff<7){
				$TEvents->set_var('event_class', " actual2");
			}

			if($tc > 5){
				if($c==5){
					$TEvents->enable('BLOCK_more_events_start');
					$TEvents->enable('BLOCK_more_events_end');
				} else {
					$TEvents->disable('BLOCK_more_events_start');
				}
			}
			$TEvents->parse_block('BLOCK_events_list', TMPL_APPEND);
			$c++;
		}

		return $this->add_right_item('Aktuāli', $TEvents->parse());
	}

	function not_found(string $msg = NULL)
	{
		$msg = $msg??"Resurss nav atrasts vai ir bloķēts!";
		$this->Index->enable('BLOCK_not_found')->set_var('msg', $msg);

		header404($msg);

		return $this;
	}

	function not_logged(string $msg = NULL)
	{
		$msg = $msg??"Pieeja tikai reģistrētiem lietotājiem!";
		$this->Index->enable('BLOCK_not_loged')->set_var('msg', $msg);

		header403($msg);

		return $this;
	}

	function forbidden(string $msg = NULL)
	{
		$msg = $msg??"Pieeja liegta!";
		$this->error($msg);

		header403($msg);

		return $this;
	}

	function error(string|array $msg = "TrueMetal")
	{
		if(is_array($msg)){
			$this->Index->enable('BLOCK_error')->set_var('error_msg', join("<br>", $msg));
		} else {
			$this->Index->enable('BLOCK_error')->set_var('error_msg', $msg);
		}

		return $this;
	}

	function msg(string|array $msg = "TrueMetal")
	{
		if(is_array($msg)){
			$this->Index->enable('BLOCK_msg')->set_var('msg', join("<br>", $msg));
		} else {
			$this->Index->enable('BLOCK_msg')->set_var('msg', $msg);
		}

		return $this;
	}
}
