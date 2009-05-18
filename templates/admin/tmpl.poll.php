<form method="post" action="{module_root}/do/" name="poll_list">
<table width="100%" cellpadding="2" cellspacing="2" border="0" align="center">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="poll_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat" colspan="3">Jautājumi</td>
	</tr>
	<!-- BEGIN BLOCK_poll disabled -->
	<input type="hidden" name="poll_id{poll_nr}" value="{poll_id}">
	<tr>
		<td class="{poll_color_class}"><input type="checkbox" name="poll_checked{poll_nr}"></td>
		<td nowrap class="{poll_color_class}"><a href="{module_root}/{poll_id}/">{poll_name}</a></td>
		<td nowrap class="{poll_color_class}">{poll_entered}</td>
		<td class="{poll_color_class}" width="100%">
		<!-- BEGIN BLOCK_poll_active disabled -->aktīvs<!-- END BLOCK_poll_active -->
		<!-- BEGIN BLOCK_poll_inactive disabled -->neaktīvs<!-- END BLOCK_poll_inactive -->
		</td>
	</tr>
	<!-- END BLOCK_poll -->
	<tr>
		<td colspan="4">Iezīmētos: <select name="action">
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

<!-- BEGIN BLOCK_pollnew --><!-- END BLOCK_pollnew -->

<!-- BEGIN BLOCK_poll_error disabled -->
<span class="error-msg">{error_msg}</span>
<br><a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_poll_error -->
