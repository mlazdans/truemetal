<form action="" method="post">
<input type="hidden" name="action" value="ub_new">
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top"><input type="submit" name="do" value="Jauns"></td>
		<td valign="top"><input type="button" value="Atcelt" onClick="location.replace('{http_root}/admin/ban/')"></td>
		<td valign="top" width=100%>&nbsp;</td>
	</tr>
</table>
</form>

<!-- BEGIN BLOCK_ban_list disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="ub_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Bani</td>
	</tr>
	<!-- BEGIN BLOCK_bans -->
	<input type="hidden" name="ub_id{ub_nr}" value="{ub_id}">
	<tr>
		<td class="{ub_color_class}"><input type="checkbox" name="ub_checked{ub_nr}"></td>
		<td class="{ub_color_class}" valign="top"><a href="{module_root}/{ub_id}/">{ub_moduleid} ({ub_net}/{ub_mask})</a> - {ub_reason}</td>
	</tr>
	<!-- END BLOCK_bans -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		</select>
		<input type="submit" value="  OK  " name="do_userbans">
		</td>
	</tr>
</table>
<input type="hidden" name="ub_count" value="{ub_count}">
</form>
<!-- END BLOCK_ban_list -->

<!-- BEGIN BLOCK_ban_error disabled -->
<span class="error-msg">{error_msg}</span>
<br><a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_ban_error -->

<!-- BEGIN BLOCK_ban_edit disabled -->
<form action="{module_root}/{ub_id}/" method="post" name="ub_edit">
<input type="hidden" name="action" value="ub_save">
<input type="hidden" name="data[ub_id]" value="{ub_id}">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
		<td nowrap valign="top">Modulis</td><td valign="top" width="100%">
		<input type="text" value="{ub_moduleid}" name="data[ub_moduleid]" size="32">
		<SELECT NAME=module_list onChange="this.form.elements['data[ub_moduleid]'].value=this.options[this.selectedIndex].value">
		<option>---</option>
		<!-- BEGIN BLOCK_module_list disabled --><option value="{module}">{module}</option><!-- END BLOCK_module_list -->
		</select>
		</td>
	</tr>
	<tr>
		<td nowrap>IP/Net</td><td width="100%"><input type="text" name="data[ub_net]" value="{ub_net}" size="20"></td>
	</tr>
	<tr>
		<td nowrap>Maska</td><td width="100%"><input type="text" name="data[ub_mask]" value="{ub_mask}" size="20"></td>
	</tr>
	<tr>
		<td>Aktīvs?</td><td><select name="data[ub_active]">
		<option value="Y"{ub_active_y}>Jā</option>
		<option value="N"{ub_active_n}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td nowrap valign="top">Iemesls</td><td width="100%"><textarea name="data[ub_reason]" cols="40" rows="5">{ub_reason}</textarea></td>
	</tr>
	<tr>
		<td nowrap>Datums</td><td width="100%"><input type="text" name="data[ub_entered]" value="{ub_entered}" size="20"></td>
	</tr>
	<tr>
		<td nowrap>Līdz</td><td width="100%"><input type="text" name="data[ub_expires]" value="{ub_expires}" size="20"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="button" value="Saglabāt" onClick="onSubmitHandler(this.form);"></td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/javascript">

function onSubmitHandler(form) {
	var err_msg = '';

	if(form.elements["data[ub_moduleid]"].value == '')
		err_msg = err_msg + 'Nav norādīts modulis\n';

	if(form.elements["data[ub_net]"].value == '')
		err_msg = err_msg + 'Nav norādīta ip adrese/tīkls\n';

	if(err_msg)
		alert('Kļūda:\n' + err_msg)
	else
		form.submit();
}

</script>
<!-- END BLOCK_ban_edit -->
