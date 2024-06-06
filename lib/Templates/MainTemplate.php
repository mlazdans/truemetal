<?php declare(strict_types = 1);

class MainTemplate extends AbstractTemplate
{
	public string $title              = '';
	public string $meta_descr         = '';
	public string $msg                = '';
	public string $tmpl_finished      = '';
	public int    $script_version     = 0;
	public bool   $disable_youtube    = false;

	public ?AbstractTemplate  $MiddleBlock = null;
	public ?TopBannerTemplate $BannerBlock = null;
	public RightTemplate      $RightBlock;
	public ErrorTemplate      $ErrorBlock;
	public MsgTemplate        $MsgBlock;

	function __construct()
	{
		global $sys_script_version;

		$this->script_version = $sys_script_version;
		$this->disable_youtube = !empty(User::get_val('l_disable_youtube'));

		$this->RightBlock = new RightTemplate;
		$this->ErrorBlock = new ErrorTemplate;
		$this->MsgBlock = new MsgTemplate;
	}

	function error(string|array $msg)
	{
		$this->ErrorBlock->enable()->msg = $msg;

		return $this;
	}

	function msg(string|array $msg)
	{
		$this->MsgBlock->enable()->msg = $msg;

		return $this;
	}

	function not_logged(string|array $msg = "Pieeja tikai reģistrētiem lietotājiem!")
	{
		$this->msg($msg);
		header403($msg);
		return $this;
	}

	function forbidden(string|array $msg = "Pieeja liegta!")
	{
		$this->error($msg);
		header403($msg);
		return $this;
	}

	function not_found(string $msg = "Resurss nav atrasts vai ir bloķēts!")
	{
		$this->msg($msg);
		header404($msg);
		return $this;
	}

	function set_title(string $title)
	{
		$this->title = $title;

		return $this;
	}

	function get_title()
	{
		return $this->title;
	}

	function set_descr(string $descr)
	{
		$this->meta_descr = $descr;

		return $this;
	}

	protected function out(): void
	{ ?>
<!DOCTYPE html>
<html lang="lv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>[ TRUEMETAL <?=specialchars($this->title) ?> ]</title>
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
		<div id="main">
		<!-- BEGIN BLOCK_container -->
			<? $this->ErrorBlock->print() ?>
			<? $this->MsgBlock->print() ?>
			<? if($this->MiddleBlock)$this->MiddleBlock->print() ?>
		<!-- END BLOCK_container -->
		</div>
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
