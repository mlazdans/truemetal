<!DOCTYPE html>
<html lang="lv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>[ TRUEMETAL {title} ]</title>
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=">
<meta name="description" content="{meta_descr}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<link href="/css/?v={script_version}" rel="stylesheet" type="text/css">
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
	<div class="banner">
		<!-- BEGIN BLOCK_banner_top -->
			<a href="{banner_href}"><img src="/img/{banner_img}" width="{banner_width}" height="{banner_height}" alt="{banner_alt}"></a>
		<!-- END BLOCK_banner_top -->
	</div>
	<div class="content" class="{block_middle_class}">
		<div id="main">
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
	</div>
	<div class="right"><!-- BEGIN BLOCK_right disabled --><!-- END BLOCK_right --></div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/jsload/?v={script_version}"></script>
<script>
	var User = {
		disableYoutube: parseInt('{disable_youtube}')
	};

	$(truemetal).ready(function(){
		// Truemetal.initMenu();
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
