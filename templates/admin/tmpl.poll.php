<form method="post" action="{module_root}/do/" id="poll_list">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="checkbox" onclick="Truemetal.checkAll(this);" />
	</td>
	<td class="TD-cat" colspan="3">
		Jautājumi
	</td>
</tr>
<!-- BEGIN BLOCK_poll disabled -->
<tr>
	<td class="{poll_color_class}">
		<input type="hidden" name="poll_id{poll_nr}" value="{poll_id}" />
		<input type="checkbox" name="poll_checked{poll_nr}" />
	</td>
	<td class="nowrap {poll_color_class}">
		<a href="{module_root}/{poll_id}/">{poll_name}</a>
	</td>
	<td class="nowrap {poll_color_class}">
		{poll_entered}
	</td>
	<td class="{poll_color_class}">
		<!-- BEGIN BLOCK_poll_active disabled -->aktīvs<!-- END BLOCK_poll_active -->
		<!-- BEGIN BLOCK_poll_inactive disabled -->neaktīvs<!-- END BLOCK_poll_inactive -->
	</td>
</tr>
<!-- END BLOCK_poll -->
<tr>
	<td colspan="4">Iezīmētos:
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

<!-- BEGIN BLOCK_pollnew --><!-- END BLOCK_pollnew -->

<!-- BEGIN BLOCK_poll_error disabled -->
<span class="error-msg">{error_msg}</span>
<span><a href="#" onclick="javascript:history.back()">Atpakaļ</a></span>
<!-- END BLOCK_poll_error -->
