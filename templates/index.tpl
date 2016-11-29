<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>[ TRUEMETAL {title} ]</title>
<meta http-equiv="Content-Language" content="lv" />
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=" />
<meta name="author" content="Mārtiņš Lazdāns (dqdp.net)" />
<meta name="description" content="{meta_descr}" />
<link href="/css/?s[]=styles-dynamic&amp;s[]=article&amp;s[]=jquery-ui&amp;v={script_version}" rel="stylesheet" type="text/css" />
</head>
<body>
<!-- BEGIN BLOCK_body -->
<div id="header">
	<div class="logo"><a href="/" class="logo-inside"></a></div>
	<div class="menu">
		<table cellpadding="5" cellspacing="0">
		<tr>
			<td><a href="/"><img src="/img/buttons/article{menu_active_article}.png?{script_version}" alt="JAUNUMI" width="94" height="20" /></a></td>
			<td><a href="/forum/"><img src="/img/buttons/forum{menu_active_forum}.png?{script_version}" alt="FORUMS" width="85" height="20" /></a></td>
			<td><a href="/reviews/"><img src="/img/buttons/reviews{menu_active_reviews}.png?{script_version}" alt="RECENZIJAS" width="124" height="20" /></a></td>
			<td><a href="/interviews/"><img src="/img/buttons/interviews{menu_active_interviews}.png?{script_version}" alt="INTERVIJAS" width="121" height="20" /></a></td>
		</tr>
		</table>
	</div>
	<div class="banner"><!-- BEGIN BLOCK_banner_top --><a href="{banner_href}"><img src="/img/{banner_img}" width="{banner_width}" height="{banner_height}" alt="{banner_alt}" /></a><!-- END BLOCK_banner_top --></div>
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
<!-- END BLOCK_body -->
<script type="text/javascript" src="/jsload/?s[]=jquery&amp;s[]=jquery-ui&amp;s[]=truemetal&amp;v={script_version}"></script>
<script type="text/javascript">
var User = {
	disableYoutube: parseInt('{disable_youtube}')
};
$(document).ready(function(){
		Truemetal.initMenu();
		Truemetal.initUnselectable();
		if(!User.disableYoutube){
			Truemetal.initYouTube();
		}
});
</script>
{tmpl_finished}
</body>
</html>

