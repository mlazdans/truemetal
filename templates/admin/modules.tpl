<!-- BEGIN BLOCK_modules_list disabled -->
<div class="TD-cat-active">
	Moduļi: saraksts
</div>

<form action="" method="post">
<table class="Main">
<tr>
	<td valign="top">
		<input type="hidden" name="action" value="module_new" />
		<input type="submit" name="do" value="Jauns" />
	</td>
	<td valign="top">
		<input type="button" value="Atcelt" onclick="location.replace('{http_root}/admin/modules/')" />
	</td>
	<td valign="top">&nbsp;</td>
</tr>
</table>
</form>

<!-- BEGIN BLOCK_nomodules disabled -->
<div>
	Moduļu nav
</div>
<!-- END BLOCK_nomodules -->

<form action="" method="post" id="module_list">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="hidden" name="module_count" value="{module_count}" />
		<input type="checkbox" name="module_check_all" onclick="Truemetal.checkAll(this);" />
	</td>
	<td class="TD-cat">Nosaukums</td>
	<td class="TD-cat">Pos</td>
</tr>
<!-- BEGIN BLOCK_modules -->
<tr>
	<td class="{module_color_class}">
		<input type="hidden" name="mod_id{module_count}" value="{mod_id}" />
		<input type="checkbox" name="mod_checked{module_count}" />
	</td>
	<td class="{module_color_class}" valign="top" style="white-space: nowrap;">
		{module_padding}<a href="{module_root}/{mod_id}/">{module_name}</a>
	</td>
	<td valign="top">{module_pos}</td>
</tr>
<!-- END BLOCK_modules -->
<tr>
	<td colspan="3">
		Iezīmētos:
		<select name="action">
			<option value="">---</option>
			<option value="delete_multiple">Dzēst</option>
			<option value="activate_multiple">Aktivizēt</option>
			<option value="deactivate_multiple">Deaktivizēt</option>
			<option value="show_multiple">Parādīt</option>
			<option value="hide_multiple">Slēpt</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_modules_list -->

<!-- BEGIN BLOCK_modules_error disabled -->
<span class="error-msg">{error_msg}</span>
<a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_modules_error -->

<!-- BEGIN BLOCK_modules_edit disabled -->
<div class="TD-cat-active">
	Moduļi: rediģēt <em>{module_name_edit}</em>
</div>

<form action="" method="post" id="module_edit">
<table class="Main">
<tr>
	<th>Zem</th>
	<td>
		<input type="hidden" name="action" value="module_save" />
		<input type="hidden" name="data[mod_id]" value="{mod_id}" />
		<select name="data[mod_modid]">
		<!-- BEGIN BLOCK_modules_under_list -->
		<option value="{mod_id}">{module_padding}{module_name}</option>
		<!-- END BLOCK_modules_under_list -->
		</select>
	</td>
</tr>
<tr>
	<th>ID</th>
	<td>
		<input type="text" name="data[module_id]" value="{module_id}" size="48" /> (pieļaujamie simboli [a-z0-9_])
	</td>
</tr>
<tr>
	<th>Nosaukums</th>
	<td>
		<input type="text" name="data[module_name]" value="{module_name}" size="48" />
	</td>
</tr>
<tr>
	<th>Aktīvs?</th>
	<td>
		<select name="data[module_active]">
			<option value="Y"{module_active_y}>Jā</option>
			<option value="N"{module_active_n}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<th>Redzams?</th>
	<td>
		<select name="data[module_visible]">
			<option value="Y"{module_visible_y}>Jā</option>
			<option value="N"{module_visible_n}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<th>Pozīcija</th>
	<td>
		<select name="data[module_pos]">
		<!-- BEGIN BLOCK_modules_pos -->
			<option value="{pos}"{pos_selected}>{pos_name}</option>
		<!-- END BLOCK_modules_pos -->
		</select>
	</td>
</tr>
<tr>
	<th>Tips</th>
	<td>
		<select name="data[module_type]">
			<option value="O"{module_type_o}>Atvērts</option>
			<option value="R"{module_type_r}>Reģistrētiem</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2">
		<textarea class="edit" name="data[module_data]" rows="15" cols="150">
			{module_data}
		</textarea>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value=" Saglabāt " />
	</td>
</tr>
</table>
</form>

<script type="text/javascript">
/*
# TODO: pieattačot tiny_mce
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
*/
</script>
<!-- END BLOCK_modules_edit -->

<!-- BEGIN BLOCK_modules_under disabled -->
<form action="{module_root}/set_module" method="post">
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
