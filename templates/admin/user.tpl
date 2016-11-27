<form action="" method="post">
<input type="hidden" name="action" value="user_new">
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top"><input type="submit" name="do" value="Jauns"></td>
		<td valign="top"><input type="button" value="Atcelt" onClick="location.replace('/admin/user/')"></td>
		<td valign="top" width="100%">&nbsp;</td>
	</tr>
</table>
</form>

<!-- BEGIN BLOCK_user_error disabled -->
<span class="error-msg">{error_msg}</span>
<br><a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_user_error -->

<!-- BEGIN BLOCK_user_list disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="user_check_all" onClick="Truemetal.checkAll(this)"></td>
		<td width="100%" class="TD-cat">Lietotāji</td>
	</tr>
	<!-- BEGIN BLOCK_users -->
	<input type="hidden" name="user_login{user_nr}" value="{user_login}">
	<tr>
		<td class="{user_color_class}"><input type="checkbox" name="user_checked{user_nr}"></td>
		<td class="{user_color_class}" valign="top"><a href="{module_root}/{user_login}/">{user_login} ({user_name})</a></td>
	</tr>
	<!-- END BLOCK_users -->
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
<input type="hidden" name="user_count" value="{user_count}">
</form>
<!-- END BLOCK_user_list -->

<!-- BEGIN BLOCK_user_edit disabled -->
<form action="{module_root}/{user_login}/" method="post" name="user_edit">
<input type="hidden" name="action" value="user_save">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
		<td>Login</th><td width="100%"><input type="text" name="data[user_login]" value="{user_login}" size="17"></td>
	</tr>
	<tr>
		<td>Parole</th><td width="100%"><input type="password" name="data[user_pass]" value="{user_pass}" size="17"></td>
	</tr>
	<tr>
		<td>Nosaukums</th><td width="100%"><input type="text" name="data[user_name]" value="{user_name}" size="48"></td>
	</tr>
	<tr>
		<td>E-pasts</th><td width="100%"><input type="text" name="data[user_email]" value="{user_email}" size="32"></td>
	</tr>
	<tr>
		<td>Mājas lapa</th><td width="100%"><input type="text" name="data[user_homepage]" value="{user_homepage}" size="48"></td>
	</tr>
	<tr>
		<td>Aktīvs?</th><td><select name="data[user_active]">
		<option value="Y"{user_active_y}>Jā</option>
		<option value="N"{user_active_n}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td nowrap>Ievadīšanas datums</th><td width="100%"><input type="text" name="data[user_entered]" value="{user_entered}" size="20"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="button" value="Saglabāt" onClick="onSubmitHandler(this.form);"></td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/javascript">

function onSubmitHandler(form) {
	var err_msg = '';

	if(form.elements["data[user_login]"].value == '')
		err_msg = err_msg + 'Nav norādīts lietotāja logins\n';

	if(form.elements["data[user_name]"].value == '')
		err_msg = err_msg + 'Nav norādīts lietotāja nosaukums\n';

	if(err_msg)
		alert('Kļūda:\n' + err_msg)
	else
		form.submit();
}

</script>
<!-- END BLOCK_user_edit -->
