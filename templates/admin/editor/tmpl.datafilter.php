<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>Data Filter</title>
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
<script language="JavaScript" type="text/javascript">
	function filter_close() {
		var ret = 0;

		if(datafilter.filter_table.checked)
			ret += parseInt(datafilter.filter_table.value);

		if(datafilter.filter_font.checked)
			ret += parseInt(datafilter.filter_font.value);

		window.returnValue = ret;
		window.close();
	}
</script>
</head>

<body>
<form name="datafilter">
<table cellpadding="2" cellspacing="0" border="0" align="center">
	<tr>
		<td colspan="2">Izfiltrēt:</td>
	</tr>
	<tr>
		<td>Tabulas</td><td><input type="checkbox" value="{REMOVE_TABLE}" name="filter_table"></td>
	</tr>
	<tr>
		<td>Fontus</td><td><input type="checkbox" value="{REMOVE_FONT}" name="filter_font"></td>
	</tr>
	<tr>
		<td colspan="2"><br>
		<input type="button" value="  OK  " onClick="filter_close();">
		<input type="button" value="Atcelt" onClick="window.close();">
		</td>
	</tr>
</table>
</form>
</body>
</html>