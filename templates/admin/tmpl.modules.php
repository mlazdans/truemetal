<form action="" method="post">
<input type="hidden" name="action" value="module_new">
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top"><input type="submit" name="do" value="Jauns"></td>
		<td valign="top"><input type="button" value="Atcelt" onClick="location.replace('{http_root}/admin/modules/')"></td>
		<td valign="top" width=100%>&nbsp;</td>
	</tr>
</table>
</form>

<!-- BEGIN BLOCK_modules_list disabled -->
<form action="" method="post">
<table width="100%" cellpadding="2" cellspacing="2" border="0" align="center">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="forum_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" colspan="3" class="TD-cat">Moduļi</td>
	</tr>
	<!-- BEGIN BLOCK_modules -->
	<input type="hidden" name="mod_id{item_count}" value="{mod_id}">
	<tr>
		<td class="{module_color_class}"><input type="checkbox" name="mod_checked{item_count}"></td>
		<td nowrap class="{module_color_class}" valign="top">{module_padding}<a href="{module_root}/{mod_id}/">{module_name}</a></td>
		<td valign="top">{module_pos}</td>
		<td width="100%"></td>
	</tr>
	<!-- END BLOCK_modules -->
	<tr>
		<td colspan="4">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		<option value="show_multiple">Parādīt</option>
		<option value="hide_multiple">Slēpt</option>
		</select>
		<input type="submit" value="  OK  ">
		</td>
	</tr>
</table>
<input type="hidden" name="item_count" value="{item_count}">
</form>
<!-- END BLOCK_modules_list -->

<!-- BEGIN BLOCK_modules_error disabled -->
<span class="error-msg">{error_msg}</span>
<a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_modules_error -->

<!-- BEGIN BLOCK_modules_edit disabled -->
<form action="{module_root}/{mod_id}/save" method="post" name="{editor_id}">
<input type="hidden" name="action" value="module_save">
<input type="hidden" name="data[mod_id]" value="{mod_id}">
<input type="hidden" name="data[mod_modid]" value="{mod_modid}">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
		<td>Zem:</th><td width="100%"><a href="{module_root}/{module_mod_id}/">{module_module_name}</a>&nbsp;</td>
	</tr>
	<tr>
		<td>ID</th><td><input type="text" name="data[module_id]" value="{module_id}" size="48"> (pieļaujamie simboli [a-z0-9_])</td>
	</tr>
	<tr>
		<td>Nosaukums</th><td><input type="text" name="data[module_name]" value="{module_name}" size="48"></td>
	</tr>
	<tr>
		<td>Aktīvs?</th><td><select name="data[module_active]">
		<option value="Y"{module_active_y}>Jā</option>
		<option value="N"{module_active_n}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td>Redzams?</th><td><select name="data[module_visible]">
		<option value="Y"{module_visible_y}>Jā</option>
		<option value="N"{module_visible_n}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td>Pozīcija</th><td><select name="data[module_pos]">
		<!-- BEGIN BLOCK_modules_pos --><option value="{pos}"{pos_selected}>{pos_name}</option><!-- END BLOCK_modules_pos -->
		</select></td>
	</tr>
	<tr>
		<td>Tips</th><td><select name="data[module_type]">
		<option value="O"{module_type_o}>Atvērts</option>
		<option value="R"{module_type_r}>Reģistrētiem</option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" width="100%"><!-- BEGIN BLOCK_editor --><!-- END BLOCK_editor --></td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/javascript">
function setUpHandler() {
	if(textEdit{editor_id}.loaded)
		textEdit{editor_id}.onSubmitHandler = onSubmitHandler;
	else {
		setTimeout('setUpHandler()',500);
	}
}

setUpHandler();

function onSubmitHandler() {
	var form = {editor_id};
	var err_msg = '';

	if(form.elements["data[module_id]"].value == '')
		err_msg = err_msg + 'Nav norādīts moduļa ID\n';
	
	if(form.elements["data[module_name]"].value == '')
		err_msg = err_msg + 'Nav norādīts moduļa nosaukums\n';

	if(err_msg)
		alert('Kļūda:\n' + err_msg)
	else
		form.submit();
}
</script>
<!-- END BLOCK_modules_edit -->

<!-- BEGIN BLOCK_modules_under disabled -->
<form action="{module_root}/set_module" method="post" name="{editor_id}">
	<input type="hidden" name="action" value="module_new">
Zem:<select name="mod_modid" onChange="this.form.submit();">
	<option name="">-Izvēlies-</option>
	<option value="-1">/</option>
	<!-- BEGIN BLOCK_modules_under_list -->
	<option value="{mod_id}">{module_padding}{module_name}</option>
	<!-- END BLOCK_modules_under_list -->
	</select>
</form>
<!-- END BLOCK_modules_under -->
