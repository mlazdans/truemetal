<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>GetModule</title>
<meta http-equiv="Pragma" content="no-cache">
<style type="text/css"><!--
body {
	font-family: "Verdana", arial, serif;
	color: black;
	background-color: ButtonFace;
}

td {
	font-size: 10pt;
	text-align: left;
	font-family: "MS Dialog", arial, serif;
}

input, select {
	font-size: 9pt;
	background-color: ButtonFace;
}

--></style>
</head>

<body>
<table width="100%" cellpadding="2" cellspacing="0" border="0" align="center">
	<tr>
		<td><h3>Izvēlies moduli</h3></td>
	</tr>
	<tr>
		<td><input type="button" value="Atcelt" onClick="window.close();" class="bt"></td>
	</tr>
	<tr>
		<td><!-- BEGIN BLOCK_modules -->{module_padding}<a href="#" onClick="window.returnValue = '{http_root}/{module_path}'; window.close();">{module_name}</a><br><br><!-- END BLOCK_modules --></td>
	</tr>
	<tr>
		<td><br><input type="button" value="Atcelt" onClick="window.close();" class="bt"></td>
	</tr>
</table>
</body>
</html>