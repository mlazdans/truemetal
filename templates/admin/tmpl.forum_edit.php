<form method="post" action="{module_root}/{forum1_id}/do/">
<table cellpadding="2" cellspacing="1" border="0" width="100%">
	<tr>
		<td width="100%" class="TD-menu">/ <a class="A-cat" href="{module_root}/">Forums</a><!-- BEGIN BLOCK_forum_path disabled --> / <a class="A-cat" href="{module_root}/{forum1_path}">{forum1_name}</a><!-- END BLOCK_forum_path --> /</td>
	</tr>
</table>

<input type="hidden" name="action" value="save_forum">
<input type="hidden" name="data[forum_id]" value="{forum1_id}">
<table cellpadding="2" cellspacing="1" border="0" width="100%">
	<tr>
		<td colspan="2">Labot forumu <u>{forum1_name}</u></td>
	</tr>
	<tr>
		<td nowrap align="right">Nosaukums:</td>
		<td><input type="text" name="data[forum_name]" value="{forum1_name}" size="32"></td>
	</tr>
	<tr>
		<td nowrap align="right">Aktīvs?:</td>
		<td><select name="data[forum_active]">
		<option value="Y"{forum1_active}>Jā</option>
		<option value="N"{forum1_inactive}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td nowrap align="right">Var būt apakštēmas?:</td>
		<td><select name="data[forum_allowchilds]">
		<option value="N"{forum1_prohibitchilds}>Nē</option>
		<option value="Y"{forum1_allowchilds}>Jā</option>
		</select></td>
	</tr>
	<tr>
		<td nowrap align="right">Ievadīts:</td>
		<td><input type="text" name="data[forum_entered]" value="{forum1_entered}"></td>
	</tr>
	<tr>
		<td nowrap align="right" valign="top">Dati:</td>
		<td width="100%"><textarea style="width: 100%" rows="8" name="data[forum_data]">{forum1_data}</textarea></td>
	</tr>
	<tr>
		<td nowrap align="right">Lietotāja vārds:</td>
		<td><input type="text" name="data[forum_username]" value="{forum1_username}"></td>
	</tr>
	<tr>
		<td nowrap align="right">Lietotāja e-pasts:</td>
		<td><input type="text" name="data[forum_useremail]" value="{forum1_useremail}"></td>
	</tr>
	<tr>
		<td nowrap align="right">Lietotāja IP:</td>
		<td><input type="text" name="data[forum_userip]" value="{forum1_userip}"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Saglabāt"></td>
	</tr>
</table>
</form>
<!-- BEGIN BLOCK_forumdets --><!-- END BLOCK_forumdets -->
<!-- BEGIN BLOCK_forumnew --><!-- END BLOCK_forumnew -->
