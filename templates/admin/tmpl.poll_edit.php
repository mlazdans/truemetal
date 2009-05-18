<form method="post" action="{module_root}/{poll1_id}/do/" name="poll_edit">
<input type="hidden" name="action" value="save_poll">
<input type="hidden" name="data[poll_id]" value="{poll1_id}">
<table cellpadding="2" cellspacing="1" border="0">
	<tr>
		<td colspan="2">Labot jautājumu <u>{poll1_name_stripped}</u></td>
	</tr>
	<tr>
		<td nowrap align="right">Nosaukums:</td>
		<td><input type="text" name="data[poll_name]" value="{poll1_name}" size="48"></td>
	</tr>
	<tr>
		<td nowrap align="right">Aktīvs?:</td>
		<td><select name="data[poll_active]">
		<option value="Y"{poll1_active}>Jā</option>
		<option value="N"{poll1_inactive}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td nowrap align="right">Starta datums:</td>
		<td><input type="text" name="data[poll_entered]" value="{poll1_entered}"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="button" value="Saglabāt" onClick="onSubmitHandler(this.form);"></td>
	</tr>
</table>
</form>
<script language="JavaScript" type="text/javascript">

function onSubmitHandler(form) {
	var err_msg = '';

	if(form.name == 'poll_edit') {
		if(form.elements["data[poll_name]"].value == '')
			err_msg = err_msg + 'Nav norādīts nosaukums\n';
	}

	if(form.name == 'poll_new') {
		if(form.elements["data[poll_name]"].value == '')
			err_msg = err_msg + 'Nav norādīts nosaukums\n';
	}

	if(err_msg)
		alert('Kļūda:\n' + err_msg)
	else
		form.submit();
}

</script>
<!-- BEGIN BLOCK_poll_error disabled -->
<span class="error-msg">{error_msg}</span>
<br><a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_poll_error -->
<!-- BEGIN BLOCK_polldets --><!-- END BLOCK_polldets -->
<!-- BEGIN BLOCK_pollnew --><!-- END BLOCK_pollnew -->