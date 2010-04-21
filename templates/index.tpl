<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>[ TRUE METAL {title} ]</title>
<meta http-equiv="Content-Language" content="lv" />
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=" />
<meta name="author" content="Mārtiņš Lazdāns (dqdp.net)" />
<meta name="description" content="{meta_descr}" />
<link rel="stylesheet" type="text/css" href="{http_root}/css/styles.css?{script_version}" />
<link rel="stylesheet" type="text/css" href="{http_root}/css/article.css?{script_version}" />
<link rel="stylesheet" type="text/css" href="{http_root}/css/jquery-ui/truemetal/jquery-ui.css?{script_version}" />
</head>

<body>

<div id="header">
	<div class="logo"><a href="{http_root}/"><img src="{http_root}/img/logo5.gif" width="580" height="80" alt="TRUE METAL" /></a></div>
	<div class="banner">
		<!-- BEGIN BLOCK_banner_top -->
		<a href="{banner_href}"><img src="{http_root}/img/{banner_img}" width="170" height="113" alt="{banner_alt}" /></a>
		<!-- END BLOCK_banner_top -->
	</div>
	<div class="menu">
		<table cellpadding="5" cellspacing="1">
		<tr>
			<td><a href="{http_root}/"><img src="{http_root}/img/butt_news.gif" alt="JAUNUMI" /></a></td>
			<td><a href="{http_root}/forum/"><img src="{http_root}/img/butt_forum.gif" alt="FORUMS" /></a></td>
			<td><a href="{http_root}/gallery/"><img src="{http_root}/img/butt_gallery.gif" alt="GALERIJA" /></a></td>
			<!--
			<td><a href="{http_root}/video/"><img src="{http_root}/img/butt_video.gif" alt="VIDEO" /></a></td>
			-->
			<td><a href="{http_root}/reviews/"><img src="{http_root}/img/butt_reviews.gif" alt="ALBUMU RECENZIJAS" /></a></td>
		</tr>
		</table>
	</div>
</div>

<div class="colmask rightmenu">
	<div class="colleft">
		<div class="col1 {block_middle_class}">
			<div class="clear"></div>
			<!-- BEGIN BLOCK_middle --><!-- END BLOCK_middle -->
		</div>
		<div class="col2">
			<!-- BEGIN BLOCK_right disabled --><!-- END BLOCK_right -->
		</div>
	</div>
</div>

<script type="text/javascript" src="{http_root}/jsload/?s[]=swfobject.js&amp;s[]=jquery.js&amp;s[]=jquery-ui.js&amp;s[]=truemetal.js"></script>
<script type="text/javascript">
var User = {
	disableYoutube: parseInt('{disable_youtube}')
};
$(document).ready(function(){
		Truemetal.initMenu();
		if(!User.disableYoutube)
			Truemetal.initYouTube();
		Truemetal.initUnselectable();
});
</script>
</body>
</html>
