<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>{title} ({USER_name})</title>
<meta http-equiv="Pragma" content="no-cache">
<style type="text/css"><!--
body {
	font-family: "Verdana", arial, serif;
	color: black;
	background-color: #EEEEEE;
	margin: 0;
}

td {
	font-size: 10pt;
	text-align: left;
}

p {
	text-align: justify;
}

hr {
	color: black;
}

input, select {
	font-size: 9pt;
}

.A-cat:link, .A-cat:visited, .A-cat:active {
	color: white;
	text-decoration: none;
	font-size: 10pt;
	font-weight: bold;
}

.A-cat:hover {
	color: white;
	text-decoration: underline;
	font-size: 10pt;
	font-weight: bold;
}

.TD-menu {
	background-color: #000066;
	color: white;
	font-size: 10pt;
	font-weight: bold;
}

.TD-menu-active {
	background-color: #990000;
	color: white;
	font-size: 10pt;
	font-weight: bold;
}


.TD-cat {
	background-color: #999999;
	color: white;
	font-size: 10pt;
	font-weight: bold;
}

.menu-button {
	border: 1px solid ButtonFace;
	padding-right: 1px;
}

.error-msg {
	font-family: "arial", serif;
	color: red;
	font-size: 10pt;
}

.raised {
	border-right: 1px solid ButtonHighlight;
	background-color: ButtonShadow;
}

.box-normal {
	background-color: #DDDDDD;
}

.box-inactive {
	background-color: #999999;
}

.box-invisible {
	background-color: #FF9999;
}

.box-inactive-invisible {
	background-color: #996666;
}

--></style>
<script language="JavaScript" type="text/javascript">
var editor_root = '{http_root}/admin/editor/';
</script>
<script language="JavaScript" type="text/javascript" src="{http_root}/js/editor.js"></script>
<script language="JavaScript" type="text/javascript" src="{http_root}/js/utils.js"></script>
</head>
<body>
<table width="100%" cellpadding="2" cellspacing="0" border="0" align="center">
	<tr>
		<td bgcolor="#DDDDDD"><h3>Admin: {USER_name} ({lang})</h3></td>
	</tr>
	<tr>
		<td bgcolor="#DDDDDD">
			<table cellpadding="2" cellspacing="2" border="0">
				<tr>
					<!-- BEGIN BLOCK_adminmodules -->
					<td class="{adminmodule_class}"><a class="A-cat" href="{admin_root}/{adminmodule_id}/">{adminmodule_name}</a></td>
					<!-- END BLOCK_adminmodules -->
					<td class="TD-menu"><a class="A-cat" href="http://norge.lv/stats/awstats.pl?config=truemetal.lv">Statistika</a></td>
					</td>
				</tr>
			</table>
		<hr></td>
	</tr>
	<tr>
		<td valign="top"><!-- BEGIN BLOCK_middle --><!-- END BLOCK_middle --></td>
	</tr>
	<tr>
		<td style="text-align: center"><hr><small>Copyright &copy; Norge Datorsistēmas 2003</small></td>
	</tr>
</table>
</body>
</html>