<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="lv-LV">
<head>
<meta http-equiv="Content-Language" content="lv" />
<meta http-equiv="Content-Type" content="text/html; charset={encoding}" />
<title>{title} ({USER_name})</title>
<meta http-equiv="Pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" href="{http_root}/admin_styles.css" />
<script type="text/javascript">
var editor_root = '{http_root}/admin/editor/';
</script>
<script type="text/javascript" src="{http_root}/js/editor.js"></script>
<script type="text/javascript" src="{http_root}/js/utils.js"></script>
</head>
<body>

<div id="header">
	<h1>Admin: {USER_name} ({lang})</h1>
</div>

<div id="menu">
<table border="0">
<tr>
	<!-- BEGIN BLOCK_adminmodules -->
	<td class="{adminmodule_class}">
		<a class="A-cat" href="{admin_root}/{adminmodule_id}/">{adminmodule_name}</a>
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

<hr />

<div id="footer">
	Copyright &copy; Norge Datorsistēmas 2003-2008; Copyright &copy; dqdp.net 2008-{year}
</div>

</body>
</html>
