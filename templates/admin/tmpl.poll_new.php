<form action="{module_root}/{poll1_id}/do/" method="post" name="poll_new">
<input type="hidden" name="action" value="add_poll">
<table width="100%" cellpadding="2" cellspacing="0" border="0">
	<tr>
		<td colspan="3" class="TD-cat">{poll_new_name}</td>
	</tr>
	<tr>
		<td align="right">Nosaukums:</td>
		<td><input type="text" name="data[poll_name]" maxlength="70" size="40"></td>
		<td width="100%"><input type="submit" value="Saglabāt"></td>
	</tr>
</table>
</form>