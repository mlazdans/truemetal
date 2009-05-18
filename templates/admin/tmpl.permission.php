<form action="" method="post">
<input type="hidden" name="action" value="up_new">
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top"><input type="submit" name="do" value="Jauns"></td>
		<td valign="top"><input type="button" value="Atcelt" onClick="location.replace('{http_root}/admin/permission/')"></td>
		<td valign="top" width=100%>&nbsp;</td>
	</tr>
</table>
</form>

<!-- BEGIN BLOCK_up_list disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="up_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Lietotāju tiesības</td>
	</tr>
	<!-- BEGIN BLOCK_ups -->
	<input type="hidden" name="up_login{up_nr}" value="{user_login}">
	<tr>
		<td class="{up_color_class}"><input type="checkbox" name="up_checked{up_nr}"></td>
		<td class="{up_color_class}" valign="top"><a href="{module_root}/{user_login}/">{user_login} ({user_name})</a></td>
	</tr>
	<!-- END BLOCK_ups -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_bylogins">Dzēst</option>
		</select>
		<input type="submit" value="  OK  " name="do_userlogins">
		</td>
	</tr>
</table>
<input type="hidden" name="up_count" value="{up_count}">
</form>
<!-- END BLOCK_up_list -->

<!-- BEGIN BLOCK_up_error disabled -->
<span class="error-msg">{error_msg}</span>
<a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_up_error -->

<!-- BEGIN BLOCK_up_edit disabled -->
<form action="{module_root}/{up_userlogin}/save" method="post" name="up_edit">
<script language="JavaScript" type="text/javascript">
function saveClick(form) {
	if(form.elements['data{perm_nr}[up_id]'].value) {
		form.elements['data{perm_nr}[up_checked]'].checked=true;
		form.elements['action'].value='save_multiple';
	} else
		form.elements['action'].value='up_save';
	form.submit();
}
</script>
<!-- BEGIN BLOCK_action disabled -->
<input type="hidden" name="action" value="up_save">
<!-- END BLOCK_action -->
<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="gallery_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Lietotāju tiesības</td>
	</tr>
<!-- BEGIN BLOCK_up_edit_list -->
<tr>
	<td valign="top" class="TD-cat"><input type="checkbox" name="data{perm_nr}[up_checked]"></td>
	<td>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#cccccc">
	<input type="hidden" name="data{perm_nr}[up_id]" value="{up_id}">
	<tr>
		<td nowrap valign="top">Tiesības</td><td valign="top" width="100%">
		<SELECT NAME=data{perm_nr}[up_permissions][] MULTIPLE size="{perm_list_size}">
		<!-- BEGIN BLOCK_perm_list disabled --><option value="{perm}"{perm_selected}>{perm}</option><!-- END BLOCK_perm_list -->
		</select>(Turot &lt;cntrl&gt; var izvēlēties vairākas opcijas)
		</td>
	</tr>
	<tr>
		<td nowrap valign="top">Modulis</td><td valign="top" width="100%">
		<input type="text" value="{up_moduleid}" name="data{perm_nr}[up_moduleid]" size="32">
		<SELECT NAME=module_list onChange="this.form.elements['data{perm_nr}[up_moduleid]'].value=this.options[this.selectedIndex].value">
		<option>---</option>
		<!-- BEGIN BLOCK_module_list disabled --><option value="{module}">{module}</option><!-- END BLOCK_module_list -->
		</select>
		</td>
	</tr>
	<tr>
		<td nowrap valign="top">Lietotājs</td><td valign="top" width="100%">
		<SELECT NAME=data{perm_nr}[up_userlogin]>
		<!-- BEGIN BLOCK_user_list disabled --><option value="{user_login}"{user_selected}>{user_login} ({user_name})</option><!-- END BLOCK_user_list -->
		</select>
		</td>
	</tr>
	<tr>
		<td nowrap>Datums</td><td width="100%"><input type="text" name="data{perm_nr}[up_entered]" value="{up_entered}" size="20">
		<input type="button" value="Saglbāt" name="do_userpermissions" onClick="saveClick(this.form)"></td>
	</tr>
	</table>
	</td>
</tr>
<!-- END BLOCK_up_edit_list -->
<!-- BEGIN BLOCK_action_list disabled -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="save_multiple">Saglabāt</option>
		<option value="delete_multiple">Dzēst</option>
		</select>
		<input type="submit" value="  OK  " name="do_userpermissions">
		</td>
	</tr>
<!-- END BLOCK_action_list -->
<!-- BEGIN BLOCK_submit disabled -->
<tr>
	<td colspan="2"><input type="submit" value="Saglabāt"></td>
</tr>
<!-- END BLOCK_submit -->
</table>
<input type="hidden" name="up_count" value="{perm_nr}">
</form>
<!-- END BLOCK_up_edit -->