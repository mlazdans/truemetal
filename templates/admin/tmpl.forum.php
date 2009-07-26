<div class="TD-cat">
	/ <a class="A-cat" href="{module_root}/">Forums</a><!-- BEGIN BLOCK_forum_path disabled --> / <a class="A-cat" href="{module_root}/{forum1_path}">{forum1_name}</a><!-- END BLOCK_forum_path --> /
</div>

<!-- BEGIN BLOCK_forum_edit disabled -->
<form action="" method="post" id="forum_edit">
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
	<td><input type="text" name="data[forum_name]" value="{forum_name}" size="64" /></td>
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

<!-- BEGIN BLOCK_forum_theme_new disabled -->
<form action="" method="post" id="forum_theme_new">
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

<!-- BEGIN BLOCK_forum_nothemes disabled -->
Nav nevienas tēmas
<!-- END BLOCK_forum_nothemes -->

<!-- BEGIN BLOCK_forum_themes disabled -->
<form action="" method="post" id="forum_themes">
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

<!-- BEGIN BLOCK_nocomments disabled -->
Nav neviena komentāra
<!-- END BLOCK_nocomments -->

<!-- BEGIN BLOCK_comments disabled -->
<form action="" method="post" id="comments">
<table class="Main">
<tr>
	<td class="TD-cat"><input type="checkbox" name="comment_check_all" onclick="checkAll(this.form, this)" /></td>
	<td colspan="4" class="TD-cat">Komentāri</td>
</tr>
<!-- BEGIN BLOCK_comment_item -->
<tr>
	<th class="{c_color_class}">
		<input type="hidden" name="c_id{c_nr}" value="{c_id}" />
		<input type="checkbox" name="c_checked{c_nr}" />
	</th>
	<th class="{c_color_class} nowrap">{c_username} ({c_userlogin})</th>
	<th class="{c_color_class}">{c_userip}</th>
	<th class="{c_color_class} nowrap">{c_entered}</th>
	<th class="{c_color_class}">
		<!-- BEGIN BLOCK_c_visible disabled -->aktīvs<!-- END BLOCK_c_visible -->
		<!-- BEGIN BLOCK_c_invisible disabled -->neaktīvs<!-- END BLOCK_c_invisible -->
	</th>
</tr>
<tr>
	<td></td>
	<td class="{c_color_class}" colspan="4">
		{c_datacompiled}
	</td>
</tr>
<!-- END BLOCK_comment_item -->
<tr>
	<td colspan="5">
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
<!-- END BLOCK_comments -->

