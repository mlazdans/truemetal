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
			$this->Index->set_array_prefix("USER_", User::data());
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
		global $i_am_admin, $sys_start_time, $sys_encoding;

		if($T instanceof TrueResponseInterface){
			$T->out();
			return;
		}

		if($T instanceof Template){
			$this->Index->set_block_string($T->parse(), 'BLOCK_middle');
		}

		$sys_end_time = microtime(true);
		$rendered = 'Rendered in: '.number_format(($sys_end_time - $sys_start_time), 4, '.', '').' sec';
		if($i_am_admin)
		{
			$finished = "<div>$rendered</div>";
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
		$data = Forum::load([
			"fields"=>array('forum.forum_id', 'res.res_name', 'forum.res_id', 'res_meta.res_comment_last_date', 'res_meta.res_comment_count'),
			"order"=>'COALESCE(res_meta.res_comment_last_date, res_entered) DESC',
			"rows"=>10,
			"forum_allow_childs"=>0,
		]);

		// printr($data);
		// die;

		if(count($data))
		{
			$TThemes = $this->add_file('forum/recent.tpl');
			foreach($data as $item)
			{
				$TThemes->enable_if(Res::hasNewComments($item), 'BLOCK_forum_r_comments_new');
				$TThemes->set_var('res_name', specialchars($item['res_name']));
				$TThemes->set_var('res_comment_count', $item['res_comment_count']);
				$TThemes->set_var('res_route', Forum::RouteFromRes($item));
				$TThemes->parse_block('BLOCK_forum_r_items', TMPL_APPEND);
			}

			$TThemes->enable('BLOCK_forum_r_more');
			return $this->add_right_item('Forums', $TThemes->parse());
			// $TThemes->parse_block('FILE_r_forum');
			// $TThemes->set_var('right_item_data', $TThemes->get_parsed_content('FILE_r_forum'), 'BLOCK_right_item');
			// $TThemes->parse_block('BLOCK_right_item', TMPL_APPEND);
		}

		return $this;
	}

	function set_online()
	{
		$TLogin = $this->add_file('right/online.tpl');

		$block = User::logged() ? 'BLOCK_online_item' : 'BLOCK_online_item_notloged';

		if($active_sessions = Logins::get_active())
		{
			$TLogin->enable($block);
		}

		foreach($active_sessions as $data)
		{
			$TLogin->set_var('online_nick', $data['l_nick']);
			$TLogin->set_var('online_hash', $data['l_hash']);
			$TLogin->parse_block($block, TMPL_APPEND);
		}

		$online_total = count($active_sessions);

		return $this->add_right_item("Online [$online_total]", $TLogin->parse());
	}

	function set_recent_comments($limit = 10)
	{
		$Article = new Article;

		$data = $Article->load([
			'order'=>'res_comment_last_date DESC',
			'rows'=>$limit,
		]);

		$T = $this->add_file('right/comment_recent.tpl');

		foreach($data as $item)
		{
			$T->enable_if(Res::hasNewComments($item), 'BLOCK_comment_r_comments_new');

			$T->set_var('res_name',  specialchars($item['res_name']));
			$T->set_var('res_comment_count', $item['res_comment_count']);
			$T->set_var('res_route', Article::RouteFromRes($item));
			$T->parse_block('BLOCK_comment_r_items', TMPL_APPEND);
		}

		$T->enable('BLOCK_comment_r_more');

		return $this->add_right_item("Komentāri", $T->parse());
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
		# TODO: get from proc/view
		return $this;

		if(!($jubs = Logins::load(new LoginsFilter(jubilars: true)))){
			return $this;
		}

		$block = User::logged() ? 'BLOCK_jub_item' : 'BLOCK_jub_item_notloged';

		$TJub = $this->add_file('right/jub.tpl');
		$TJub->enable($block);

		foreach($jubs as $data)
		{
			$age = round($data['age'] / 365);

			$jub_year = '';
			if($age == 0){
				$jub_year = 'jauniņais';
			} elseif($age == 1){
				$jub_year = ' gadiņš';
			} else {
				if((substr((string)$age, -2) != 11) && ($age % 10 == 1)){
					$jub_year = ' gads';
				} else {
					$jub_year = ' gadi';
				}
			}

			$TJub->set_var('l_nick', $data['l_nick']);
			$TJub->set_var('l_hash', $data['l_hash']);

			if($age){
				$TJub->set_var('jub_info', " ($age $jub_year)");
			} else {
				$TJub->set_var('jub_info', " ($jub_year)");
			}
			$TJub->parse_block($block, TMPL_APPEND);
		}

		return $this->add_right_item("Jubilāri", $TJub->parse());
	}

	function set_events()
	{
		$forum = new Forum;
		$data = $forum->load(array(
			"fields"=>array('forum.forum_id', 'res.res_name', 'forum.event_startdate', 'forum.res_id'),
			"actual_events"=>true,
			"order"=>'forum.event_startdate',
		));

		if(!$data){
			return;
		}

		$TEvents = $this->add_file('right/events.tpl');

		$c = 0;
		$tc = count($data);
		foreach($data as $item){
			$ts = strtotime($item['event_startdate']);
			$D = date('j', $ts);
			$Dw = date('w', $ts);
			$M = date('m', $ts);

			$diff = floor(($ts - time()) / (3600 * 24));

			$TEvents->set_var('event_class', "");
			$TEvents->set_var('event_url', Forum::RouteFromRes($item));
			$TEvents->set_var('event_title', ent($D.". ".get_month($M - 1).", ".get_day($Dw - 0)));
			//$TEvents->set_var('event_name', ent($item['forum_name']));
			$TEvents->set_var('event_name', $item['res_name']);

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
		$msg = $msg??"Resurss nav atrasts!";
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
