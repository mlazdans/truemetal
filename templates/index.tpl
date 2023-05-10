<!DOCTYPE html>
<html lang="lv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>[ TRUEMETAL {title} ]</title>
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=">
<meta name="description" content="{meta_descr}">
<link href="/css/?v={script_version}" rel="stylesheet" type="text/css">
</head>
<body>
<!-- BEGIN BLOCK_body -->
<div id="header">
	<div class="logo"><a href="/" class="logo-inside"></a></div>
	<div class="menu">
		<table cellpadding="5" cellspacing="0">
		<tr>
			<td><a href="/"><img src="/img/buttons/article{menu_active_article}.png" alt="JAUNUMI" width="94" height="20"></a></td>
			<td><a href="/forum/"><img src="/img/buttons/forum{menu_active_forum}.png" alt="FORUMS" width="85" height="20"></a></td>
			<td><a href="/reviews/"><img src="/img/buttons/reviews{menu_active_reviews}.png" alt="RECENZIJAS" width="124" height="20"></a></td>
			<td><a href="/interviews/"><img src="/img/buttons/interviews{menu_active_interviews}.png" alt="INTERVIJAS" width="121" height="20"></a></td>
		</tr>
		</table>
	</div>
	<div class="banner"><!-- BEGIN BLOCK_banner_top --><a href="{banner_href}"><img src="/img/{banner_img}" width="{banner_width}" height="{banner_height}" alt="{banner_alt}"></a><!-- END BLOCK_banner_top --></div>
</div>
<div class="colmask rightmenu">
	<div class="colleft">
		<div class="col1 {block_middle_class}">
			<div class="clear"></div>
			<!-- BEGIN BLOCK_container -->
				<!-- BEGIN BLOCK_error disabled -->
					<div class="TD-cat">Kļūda:</div>
					<div class="Info error-form">{error_msg}</div>
				<!-- END BLOCK_error -->
				<!-- BEGIN BLOCK_msg disabled --><div class="Info">{msg}</div><!-- END BLOCK_msg -->
				<!-- BEGIN BLOCK_not_loged disabled --><div class="Info">{msg}</div><!-- END BLOCK_not_loged -->
				<!-- BEGIN BLOCK_not_found disabled --><div class="Info">{msg}</div><!-- END BLOCK_not_found -->
				<!-- BEGIN BLOCK_middle --><!-- END BLOCK_middle -->
			<!-- END BLOCK_container -->
		</div>
		<div class="col2">
			<!-- BEGIN BLOCK_right disabled --><!-- END BLOCK_right -->
		</div>
	</div>
</div>
<!-- END BLOCK_body -->
<script src="/jsload/?v={script_version}"></script>
<script>
	var User = {
		disableYoutube: parseInt('{disable_youtube}')
	};

	$(truemetal).ready(function(){
		Truemetal.initMenu();
		Truemetal.initUnselectable();
		Truemetal.initProfiles();
		if(!User.disableYoutube){
			Truemetal.initYouTube();
		}
	});
</script>
{tmpl_finished}
</body>
</html>
