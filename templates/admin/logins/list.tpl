<div class="TD-cat-active">
	Logini: saraksts
</div>

<!-- BEGIN BLOCK_nologins disabled -->
<div>
Nav neviena logina
</div>
<!-- END BLOCK_nologins -->

<!-- BEGIN BLOCK_logins_list disabled -->
<form action="" method="post" id="logins_list">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="hidden" name="logins_count" value="{logins_count}" />
		<input type="checkbox" name="logins_check_all" onclick="Truemetal.checkAll(this);" />
	</td>
	<td class="TD-cat">Logins</td>
	<td class="TD-cat">Niks</td>
	<td class="TD-cat">Pievienojies</td>
</tr>
<!-- BEGIN BLOCK_logins -->
<tr>
	<td class="{l_color_class}">
		<input type="hidden" name="l_id{logins_nr}" value="{l_id}" />
		<input type="checkbox" name="l_checked{logins_nr}" />
	</td>
	<td class="{l_color_class}" valign="top">
		<a href="{module_root}/{l_id}/">{l_login}</a>
	</td>
	<td class="{l_color_class}" valign="top">{l_nick}</td>
	<td class="{l_color_class}" valign="top">{l_entered}</td>
</tr>
<!-- END BLOCK_logins -->
<tr>
	<td colspan="5">
		Iezīmētos:
		<select name="action">
			<option value="">---</option>
			<option value="activate_multiple">Aktivizēt</option>
			<option value="deactivate_multiple">Deaktivizēt</option>
			<option value="delete_multiple">Dzēst</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_logins_list -->

