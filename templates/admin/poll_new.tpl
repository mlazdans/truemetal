<form action="{module_root}/{poll1_id}/do/" method="post" id="poll_new">
<table class="Main">
<tr>
	<td colspan="3" class="TD-cat">
		<input type="hidden" name="action" value="add_poll" />
		{poll_new_name}
	</td>
</tr>
<tr>
	<th>Nosaukums:</th>
	<td><input type="text" name="data[poll_name]" maxlength="70" size="40" /></td>
	<td><input type="submit" value="Saglabāt" /></td>
</tr>
</table>
</form>