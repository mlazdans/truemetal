<form method="post" action="{module_root}/do/" name="forum_list">
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="forum_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Forums</td>
	</tr>
	<!-- BEGIN BLOCK_forum disabled -->
	<input type="hidden" name="forum_id{forum_nr}" value="{forum_id}">
	<tr>
		<td class="{forum_color_class}"><input type="checkbox" name="forum_checked{forum_nr}"></td>
		<td width="100%" class="{forum_color_class}">{forum_padding}<a href="{module_root}/{forum_id}/">{forum_name}</a>,
		<!-- BEGIN BLOCK_forum_active disabled -->aktīvs<!-- END BLOCK_forum_active -->
		<!-- BEGIN BLOCK_forum_inactive disabled -->neaktīvs<!-- END BLOCK_forum_inactive -->
		</td>
	</tr>
	<!-- END BLOCK_forum -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		</select>
		<input type="submit" value="  OK  ">
		</td>
	</tr>
</table>
<input type="hidden" name="item_count" value="{item_count}">
</form>

<!-- BEGIN BLOCK_forumnew --><!-- END BLOCK_forumnew -->