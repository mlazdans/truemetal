<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta http-equiv="Content-Language" content="lv">
<meta http-equiv="Content-Type" content="text/html; charset={encoding}">
<title>Faila ievietošana</title>
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

.in, select {
	font-size: 9pt;
	background-color: white;
}

input {
	font-size: 9pt;
}

--></style>
<script language="JavaScript" type="text/javascript" src="{http_root}/js/editor.js"></script>
<script language="JavaScript" type="text/javascript">
function _close(editor, filt, val) {
	if(filt == 'image')
		_image_insert(editor, val, window.opener);
	else
		_file_insert(editor, val, window.opener);
	window.close();
}
</script>
</head>

<table cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td>Filtrs:</td>
	</tr>
	<tr>
		<td><select name="filter" onChange="location.replace('{http_root}/admin/editor/getfile/{editor}/' + this.options[this.selectedIndex].value)">
		<!-- BEGIN BLOCK_filters --><option value="{filter_id}"{filter_selected}>{filter_name}</option><!-- END BLOCK_filters -->
		</select></td>
	</tr>
	<tr>
		<td><hr>Izvēlieties kādu no failiem, vai uzkopējiet jaunu!<hr></td>
	</tr>
	<form action="{http_root}/admin/upload/" method="post" enctype="multipart/form-data">
	<tr>
		<td>
<input type="file" name="some_file" class="in"><br>
<input type="hidden" name="action" value="upload">
<input type="hidden" name="hide" value="yes">
<input type="hidden" name="filter" value="{filter}">
		</td>
	</tr>
	<!-- BEGIN BLOCK_image disabled -->
	<tr>
		<td>Izmērs: <select name="image_size">
		<option value="0">default</option>
		<option value="200">200</option>
		<option value="300">300</option>
		<option value="400">400</option>
		<option value="500">500</option>
		</select>
		</td>
	</tr>
	<!-- END BLOCK_image -->
	<tr>
		<td>
		<input type="submit" name="ok" value="OK" onClick="this.value='Uzgaidiet!'; this.disabled=true; this.form.submit();">
		<input type="button" value="Atcelt" onClick="window.close();" class="bt">
		<input type="button" value="Atjaunot" onClick="window.location.reload()" class="bt">
		</td>
	</tr>
	</form>
	<tr>
		<td><hr>Esošie faili (uzklikšķināt):</td>
	</tr>
	<tr>
		<td>
		<!-- BEGIN BLOCK_file -->{nr}.&nbsp;<a href="#" onClick="_close('{editor}', '{filter}', '{http_root}/data/{file_name}');">{file_name}</a><br><!-- END BLOCK_file -->
		</td>
	</tr>
</table>
</body>
</html>