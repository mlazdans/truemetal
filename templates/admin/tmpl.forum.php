<table class="Main">
<tr>
	<td class="TD-cat">/ <a class="A-cat" href="{module_root}/">Forums</a><!-- BEGIN BLOCK_forum_path disabled --> / <a class="A-cat" href="{module_root}/{forum1_path}">{forum1_name}</a><!-- END BLOCK_forum_path --> /</td>
</tr>
</table>

<!-- BEGIN BLOCK_forum_edit disabled -->
<form method="post" action="{module_root}/{forum_id}/do/">
<table class="Main">
<tr>
	<td class="TD-cat" colspan="2">
		<input type="hidden" name="action" value="save_forum" />
		<input type="hidden" name="data[forum_id]" value="{forum_id}" />
		Labot forumu <em>{forum_name}</em>
	</td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Nosaukums:</td>
	<td><input type="text" name="data[forum_name]" value="{forum_name}" size="32" /></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Aktīvs?:</td>
	<td><select name="data[forum_active]">
	<option value="Y"{forum_active_sel}>Jā</option>
	<option value="N"{forum_inactive_sel}>Nē</option>
	</select></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Var būt apakštēmas?:</td>
	<td><select name="data[forum_allowchilds]">
	<option value="N"{forum_prohibitchilds_sel}>Nē</option>
	<option value="Y"{forum_allowchilds_sel}>Jā</option>
	</select></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Ievadīts:</td>
	<td><input type="text" name="data[forum_entered]" value="{forum_entered}" /></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;" valign="top">Dati:</td>
	<td><textarea style="width: 90%" rows="8" cols="60" name="data[forum_data]">{forum_data}</textarea></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Lietotāja vārds:</td>
	<td><input type="text" name="data[forum_username]" value="{forum_username}" /></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Lietotāja e-pasts:</td>
	<td><input type="text" name="data[forum_useremail]" value="{forum_useremail}" /></td>
</tr>
<tr>
	<td style="white-space: nowrap; text-align: right;">Lietotāja IP:</td>
	<td><input type="text" name="data[forum_userip]" value="{forum_userip}" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value="Saglabāt" /></td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_edit -->

<!-- BEGIN BLOCK_forum_nothemes disabled -->
Nav nevienas tēmas
<!-- END BLOCK_forum_nothemes -->

<!-- BEGIN BLOCK_forum_themes disabled -->
<form method="post" action="{module_root}/do/" id="forum_theme">
<table class="Main">
<tr>
	<td class="TD-cat"><input type="checkbox" name="forum_check_all" onclick="checkAll(this.form, this)" /></td>
	<td colspan="2" class="TD-cat">Tēmas</td>
</tr>
<!-- BEGIN BLOCK_forum_theme_item -->
<tr>
	<td class="{forum_color_class}">
		<input type="hidden" name="forum_id{forum_nr}" value="{forum_id}" />
		<input type="checkbox" name="forum_checked{forum_nr}" />
	</td>
	<td class="{forum_color_class}" style="white-space: nowrap;">
		{forum_padding}<a href="{module_root}/{forum_id}/">{forum_name}</a>
	</td>
	<td class="{forum_color_class}">
		<!-- BEGIN BLOCK_forum_active disabled -->aktīvs<!-- END BLOCK_forum_active -->
		<!-- BEGIN BLOCK_forum_inactive disabled -->neaktīvs<!-- END BLOCK_forum_inactive -->
	</td>
</tr>
<!-- END BLOCK_forum_theme_item -->
<tr>
	<td colspan="3">
		<input type="hidden" name="item_count" value="{item_count}" />
		Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_themes -->

<!-- BEGIN BLOCK_forum_theme_new disabled -->
<form action="{module_root}/{forum_id}/do/" method="post">
<table class="Main">
<tr>
	<td colspan="3" class="TD-cat">
		<input type="hidden" name="action" value="add_forum" />
		Jauna tēma
	</td>
</tr>
<tr>
	<td>Nosaukums:</td>
	<td><input type="text" name="data[forum_name]" maxlength="32" size="20" /></td>
	<td><input type="submit" value="Pievienot" /></td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_theme_new -->

