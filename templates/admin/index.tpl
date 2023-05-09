<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv-LV">
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>{title} ({USER_name})</title>
<meta http-equiv="Pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="/css/admin_styles.css?{script_version}">
<link rel="stylesheet" type="text/css" href="/css/admin/jquery-ui.css?{script_version}">
<script type="text/javascript" src="/js/tiny.config.js?{script_version}"></script>
<script type="text/javascript" src="/js/jquery.js?{script_version}"></script>
<script type="text/javascript" src="/js/jquery-ui.js?{script_version}"></script>
<script type="text/javascript" src="/js/truemetal.js?{script_version}"></script>
<script type="text/javascript" src="/js/admin.js?{script_version}"></script>
<script type="text/javascript" src="/tiny_mce/tiny_mce.js?{script_version}"></script>
<script type="text/javascript" src="/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php?{script_version}"></script>

<script type="text/javascript">
function initEditor(){
	tinyMCE.init(TINY_OPTIONS);
} // initEditor

function ajaxfilemanager(field_name, url, type, win) {
	var ajaxfilemanagerurl = "/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
	switch (type) {
		case "image":
			break;
		case "media":
			break;
		case "flash":
			break;
		case "file":
			break;
		default:
			return false;
	}
	tinyMCE.activeEditor.windowManager.open({
		url: ajaxfilemanagerurl,
		width: 782,
		height: 440,
		inline: "yes",
		close_previous: "no"
	},{
		window: win,
		input: field_name
	});
}

initEditor();

</script>
</head>
<body>

<div id="header">
	<h1>Admin: {USER_name}</h1>
</div>

<div id="menu">
<table border="0">
<tr>
	<!-- BEGIN BLOCK_adminmodules -->
	<td class="{adminmodule_class}">
		<a class="A-cat" href="/admin/{adminmodule_id}/">{adminmodule_name}</a>
	</td>
	<!-- END BLOCK_adminmodules -->
	<td class="TD-menu">
		<a class="A-cat" href="http://aw.dqdp.net/awstats/awstats.pl?config=truemetal.lv">Statistika</a>
	</td>
</tr>
</table>
</div>

<div id="middle">
<!-- BEGIN BLOCK_middle --><!-- END BLOCK_middle -->
</div>

<hr>

<div id="footer">
	Copyright &copy; Norge Datorsistēmas 2003-2008<br/>
	Copyright &copy; dqdp.net 2008-{year}<br/>
</div>

<hr>
{tmpl_finished}

</body>
</html>
