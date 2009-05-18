<form method="post" action="{module_root}/{forum1_id}/do/">
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="forum_theme_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Foruma tēmas</td>
	</tr>
	<!-- BEGIN BLOCK_forum disabled -->
	<input type="hidden" name="forum_id{forum_nr}" value="{forum_id}">
	<tr>
		<td class="{forum_color_class}"><input type="checkbox" name="forum_checked{forum_nr}"></td>
		<td width="100%" class="{forum_color_class}"><a href="{module_root}/{forum_id}/">{forum_name}</a> {forum_useremail} {forum_entered},
		<!-- BEGIN BLOCK_forum_active disabled -->aktīvs<!-- END BLOCK_forum_active -->
		<!-- BEGIN BLOCK_forum_inactive disabled -->neaktīvs<!-- END BLOCK_forum_inactive -->, {forum_userip} 
		</td>
	</tr>
	<!-- END BLOCK_forum -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		<option value="move_multiple">Pārvietot</option>
		</select> <select name="new_forum_forumid">
		<option value="0">/root/</option>
		<!-- BEGIN BLOCK_forum_forumid disabled -->
		<option value="{new_forum_forumid}"{forum_forumid_selected}>{new_forum_name}</option>
		<!-- END BLOCK_forum_forumid -->
		</select>
		<input type="submit" value="  OK  ">
		</td>
	</tr>
</table>
<input type="hidden" name="item_count" value="{item_count}">
</form>
