<form method="post" action="{module_root}/{poll1_id}/do/">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="checkbox" onclick="Truemetal.checkAll(this)" />
	</td>
	<td class="TD-cat">Atbildes</td>
</tr>
<!-- BEGIN BLOCK_poll disabled -->
<tr>
	<td class="{poll_color_class}">
		<input type="hidden" name="poll_id{poll_nr}" value="{poll_id}" />
		<input type="checkbox" name="poll_checked{poll_nr}" />
	</td>
	<td class="{poll_color_class}">
		<a href="{module_root}/{poll_id}/">{poll_name}</a>,
		<!-- BEGIN BLOCK_poll_active disabled -->aktīvs<!-- END BLOCK_poll_active -->
		<!-- BEGIN BLOCK_poll_inactive disabled -->neaktīvs<!-- END BLOCK_poll_inactive -->
	</td>
</tr>
<!-- END BLOCK_poll -->
<tr>
	<td colspan="2">
		Iezīmētos:
		<select name="action">
			<option value="">---</option>
			<option value="delete_multiple">Dzēst</option>
			<option value="activate_multiple">Aktivizēt</option>
			<option value="deactivate_multiple">Deaktivizēt</option>
		</select>
		<input type="hidden" name="item_count" value="{item_count}" />
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>