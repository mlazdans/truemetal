<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>[ TRUE METAL {title} ]</title>
<meta http-equiv="Content-Language" content="lv" />
<meta name="verify-v1" content="1T6p5COcolqsK65q0I6uXdMjPuPskp2jyWjFMTOW/LY=" />
<meta name="author" content="Mārtiņš Lazdāns (dqdp.net)" />
<meta name="description" content="Metāls Latvijā" />
<link rel="stylesheet" type="text/css" href="{http_root}/css/styles.css?{script_version}" />
<link rel="stylesheet" type="text/css" href="{http_root}/css/article.css?{script_version}" />
<script type="text/javascript" src="{http_root}/js/swfobject.js?{script_version}"></script>
<script type="text/javascript" src="{http_root}/js/jquery.js?{script_version}"></script>
<script type="text/javascript" src="{http_root}/js/truemetal.js?{script_version}"></script>
</head>

<body>

<!-- BEGIN BLOCK_not_loged disabled -->
<div class="Info">
	TrueMetal!
</div>
<!-- END BLOCK_not_loged -->

<!-- BEGIN BLOCK_no_such_login disabled -->
<div class="Info">
	Šāds profils neeksitē!
</div>
<!-- END BLOCK_no_such_login -->

<!-- BEGIN BLOCK_profile disabled -->
<div style="background: #444444;">
	<div class="TD-cat">
		Profils: {l_nick}
	</div>

	<!-- BEGIN BLOCK_nopicture disabled -->
	<div class="List-item">
		Bildes nav!
	</div>
	<!-- END BLOCK_nopicture -->

	<!-- BEGIN BLOCK_picture disabled -->
	<div style="margin: 0 2px; float: left;">
		<a
			href="{module_root}/view/{l_login}/"
			onclick="Truemetal.Pop('{http_root}/user/viewimage/{l_login}/', {pic_w}, {pic_h}); return false;"
		><img
			src="{pic_path}"
			alt=""
		/></a>
	</div>
	<!-- END BLOCK_picture -->

	<div class="List-item">
		<b>Pievienojies:</b> {l_entered_f}
	</div>

	<!-- BEGIN BLOCK_public_email disabled -->
	<div class="List-item">
		<b>E-pasts:</b> <a href="mailto:{l_email}">{l_email}</a>
	</div>
	<!-- END BLOCK_public_email -->

	<!-- BEGIN BLOCK_disable_comments disabled -->
	<form method="post" action="">
	<input type="hidden" name="action" value="disable_comments" />
	<div class="List-item">
		<label for="disable_comments">
			<input type="checkbox" name="disable_comments" id="disable_comments"{disable_comments_checked} />
			Nerādīt šī lietotāja komentārus
		</label>
		<input type="submit" value="OK" />
	</div>
	</form>
	<!-- END BLOCK_disable_comments -->

	<div class="List-item clear" style="text-align: center;">
		<a href="#" onclick="window.close(); return false;">Aizvērt</a>
	</div>

</div>
<!-- END BLOCK_profile -->

</body>
</html>
