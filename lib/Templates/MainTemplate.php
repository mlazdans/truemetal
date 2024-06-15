<?php declare(strict_types = 1);

class MainTemplate extends AbstractTemplate
{
	public string $title              = '';
	public string $meta_descr         = '';
	public string $tmpl_finished      = '';
	public int    $script_version     = 0;
	public bool   $disable_youtube    = false;
	public float  $sys_start_time     = 0;

	public ?AbstractTemplate  $MiddleBlock = null;
	public ?TopBannerTemplate $BannerBlock = null;
	public RightTemplate      $RightBlock;

	private array $msg       = [];
	private array $error_msg = [];

	function __construct()
	{
		global $sys_script_version, $sys_start_time;

		$this->sys_start_time = $sys_start_time;
		$this->script_version = $sys_script_version;
		$this->disable_youtube = !empty(User::get_val('l_disable_youtube'));

		$this->RightBlock = new RightTemplate;
		$this->set_descr("Metāls Latvijā");
		$this->set_banner_top();
	}

	function error(string|array $msg): static
	{
		if(is_array($msg)){
			$this->error_msg = array_merge($this->error_msg, $msg);
		} else {
			$this->error_msg[] = $msg;
		}

		return $this;
	}

	function msg(string|array $msg): static
	{
		if(is_array($msg)){
			$this->msg = array_merge($this->msg, $msg);
		} else {
			$this->msg[] = $msg;
		}

		return $this;
	}

	function not_logged(string|array $msg = "Pieeja tikai reģistrētiem lietotājiem!"): static
	{
		$this->msg($msg);
		header403($msg);
		return $this;
	}

	function forbidden(string|array $msg = "Pieeja liegta!"): static
	{
		$this->error($msg);
		header403($msg);
		return $this;
	}

	function not_found(string $msg = "Resurss nav atrasts vai ir bloķēts!"): static
	{
		$this->msg($msg);
		header404($msg);
		return $this;
	}

	function set_title(string $title): static
	{
		$this->title = $title;

		return $this;
	}

	function get_title(): string
	{
		return $this->title;
	}

	function set_descr(string $descr): static
	{
		$this->meta_descr = $descr;

		return $this;
	}

	function set_right_defaults(): void
	{
		$this->set_events();
		$this->set_recent_forum();
		$this->set_online();
		$this->set_login();
		$this->set_search();
		$this->set_jubilars();
		$this->set_recent_comments();
	}

	function set_jubilars(): void
	{
		$jubs = (new ViewJubilarsEntity)->get_all();

		if(!$jubs->count()){
			return;
		}

		$T = new JubilarsTemplate;
		$T->name = "Jubilāri";
		$T->is_logged = User::logged();
		$T->data = $jubs;

		$this->RightBlock->add_item($T);
	}

	function set_login(): void
	{
		$T = new LoginFormTemplate;
		$T->login_nick = User::get_val('l_nick');
		if($T->is_logged = User::logged()) {
			$T->referer = $_SERVER["REQUEST_URI"];
		}
		$T->name = $T->is_logged ? "Login" : "Pieslēgties";

		$this->RightBlock->add_item($T);
	}

	function set_online(): void
	{
		$active_sessions = Logins::get_active();
		$online_count = $active_sessions->count();

		if(!$online_count) {
			return;
		}

		$T = new OnlineTemplate;
		$T->is_logged = User::logged();
		$T->active_sessions = $active_sessions;
		$T->name = "Online [$online_count]";

		$this->RightBlock->add_item($T);
	}

	function set_recent_forum(): void
	{
		$F = (new ResForumFilter(forum_allow_childs: 0))
		->rows(10)
		->orderBy("COALESCE(res_comment_last_date, res_entered) DESC")
		->fields('forum_id', 'res_name', 'res_id', 'res_comment_last_date', 'res_comment_count', 'res_route', 'res_entered')
		;

		if($data = (new ViewResForumEntity)->get_all($F)){
			$T = new CommentsRecentTemplate;
			$T->data = $data;
			$T->name = "Forums";
			$this->RightBlock->add_item($T);
		}
	}

	function set_recent_comments($limit = 10)
	{
		$F = (new ResArticleFilter())->orderBy('res_comment_last_date DESC')->rows($limit);
		if($data = (new ViewResArticleEntity)->get_all($F)){
			$T = new CommentsRecentTemplate;
			$T->data = $data;
			$T->name = "Komentāri";
			$this->RightBlock->add_item($T);
		}
	}

	function set_search($search_q = ''): void
	{
		$T = new SearchFormTemplate();
		$T->search_q = $search_q;

		$this->RightBlock->add_item($T);
	}

	function set_events(): void
	{
		$F = (new ResForumFilter(actual_events: true))
		->orderBy('event_startdate')
		->fields('forum_id', 'res_name', 'event_startdate', 'res_id', 'res_route')
		;

		$data = (new ViewResForumEntity())->get_all($F);

		if(!$data->count()){
			return;
		}

		$TEvents = new EventsTemplate;
		$TEvents->items = $data;

		$this->RightBlock->add_item($TEvents);
	}

	function set_banner_top()
	{
		global $sys_top_banners;

		if(empty($sys_top_banners)){
			return;
		}

		$ban_id = mt_rand(0, count($sys_top_banners) - 1);
		$banner = $sys_top_banners[$ban_id];

		$BT = new TopBannerTemplate;
		$BT->banner_img = $banner['img'];
		$BT->banner_alt = $banner['alt'];
		$BT->banner_href = $banner['href'];

		$this->BannerBlock = $BT;
	}

	protected function container(): void
	{
		if($this->error_msg) { ?>
			<div class="TD-cat">Kļūda:</div>
			<div class="Info error-form"><?=(is_array($this->msg) ? join("<br>", $this->msg) : $this->msg) ?></div><?
		}

		if($this->msg) { ?>
			<div class="Info"><?=join("<br>", $this->msg) ?></div><?
		}

		if($this->MiddleBlock)$this->MiddleBlock->print();
	}

	protected function out(): void
	{
		$sys_end_time = microtime(true);
		$mem_usage = sprintf("Mem usage: %s MB\n", number_format(memory_get_peak_usage(true)/1024/1204, 2));
		$rendered = sprintf("Rendered in: %s sec\n", number_format(($sys_end_time - $this->sys_start_time), 4, '.', ''));
		$finished = "<div><pre>$mem_usage$rendered</pre></div>";
		$this->tmpl_finished = $finished;

		header('Content-Type: text/html; charset=utf-8');
		// header('Content-Type: text/plain; charset=utf-8');
		header('X-Powered-By: TRUEMETAL');

		?>
<!DOCTYPE html>
<html lang="lv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>[ TRUEMETAL<?=($this->title ? " - ".specialchars($this->title) : "") ?> ]</title>
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=">
<meta name="description" content="<?=specialchars(trim($this->meta_descr)) ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<link href="/css/?v=<?=$this->script_version ?>" rel="stylesheet" type="text/css">
</head>
<body>

<div id="container">
	<div class="logo"><a href="/"></a></div>
	<div class="menu">
		<a href="/" class="menu-root"></a>
		<a href="/forum/" class="menu-forum"></a>
		<a href="/reviews/" class="menu-reviews"></a>
		<a href="/interviews/" class="menu-interviews"></a>
	</div>
	<div class="banner"><? if($this->BannerBlock)$this->BannerBlock->print() ?></div>
	<div class="content">
		<div id="main"><? $this->container() ?></div>
	</div>
	<div class="right"><? $this->RightBlock->print() ?></div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/jsload/?v=<?=$this->script_version ?>"></script>
<script>
	var User = {
		disableYoutube: parseInt('<?=$this->disable_youtube ?>')
	};

	$(truemetal).ready(function(){
		// Truemetal.initMenu();
		Truemetal.initUnselectable();
		Truemetal.initProfiles();
		if(!User.disableYoutube){
			Truemetal.initYouTube();
		}
		Truemetal.highlightSelectedComment();
		Truemetal.disableOnSubmit();
		$(window).on('hashchange', Truemetal.highlightSelectedComment);
	});
</script>
<?=$this->tmpl_finished ?>
</body>
</html><?
	}
}
