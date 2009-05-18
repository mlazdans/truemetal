<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>Saites parametri</title>
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
<script language="javascript">
  function okClick() {
  }

  function cancelClick() {
		window.opener.focus();
    window.close();
  }
</script>
</head>

<body dir="ltr">
<table border="0" cellspacing="0" cellpadding="2" width="100%">
<form name="img_prop">
<tr>
  <td>Tips:</td>
  <td>
  <select name="link_type" size="1" class="input" onChange="this.form.link_url.value=this.options[selectedIndex].value">
    <option value="http://">http://</option>
    <option value="ftp://">ftp://</option>
  </select>
	</td>
</tr>
<tr>
  <td>URL:</td>
  <td><input type="text" name="link_url" class="input" size="48"></td>
</tr>
<tr>
  <td>Atvērt:</td>
  <td align="left">
  <select name="link_target" size="1" class="input">
    <option value="">Pašā lapā</option>
    <option value="_blank">Jaunā lapā</option>
  </select>
  </td>
</tr>
<tr>
<td colspan="2" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="2" align="right" valign="bottom" nowrap>
<input type="button" value="   OK   " onClick="okClick()" class="bt">
<input type="button" value="Atcelt" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>

</body>
</html>
