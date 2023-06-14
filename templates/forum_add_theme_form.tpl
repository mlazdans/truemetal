<a name="add_theme"></a>
<form action="#add_theme" method="post">

<!-- BEGIN BLOCK_forum_error disabled -->
<div class="error">{error_msg}</div>
<!-- END BLOCK_forum_error -->

<table class="Forum-Theme-form" cellpadding="2" cellspacing="1">
<tr>
	<td align="right">
		<input type="hidden" name="action" value="add_theme">
		Segvārds:
	</td>
	<td style="width: 100%;">{USER_l_nick}</td>
</tr>
<tr>
	<td style="white-space: nowrap;" align="right"{error_forum_name}>Jauna tēma:</td>
	<td><input style="width: 95%;" type="text" name="data[forum_name]" maxlength="64" size="64" value="{forum_name}"></td>
</tr>
<tr>
	<td align="right" valign="top"{error_forum_data}>Ziņa:</td>
	<td><textarea style="width: 95%;" name="data[forum_data]" cols="50" rows="10">{forum_data}</textarea></td>
</tr>
<tr>
	<td align="right">&nbsp;</td>
	<td><input type="submit" value="Pievienot"></td>
</tr>
</table>
</form>
