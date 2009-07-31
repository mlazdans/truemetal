<form method="post" action="{module_root}/{poll1_id}/do/" id="poll_edit">
<table class="Main">
<tr>
	<td class="TD-cat" colspan="2">
		<input type="hidden" name="action" value="save_poll" />
		<input type="hidden" name="data[poll_id]" value="{poll1_id}" />
		Labot jautājumu <em>{poll1_name_stripped}</em>
	</td>
</tr>
<tr>
	<th align="right">Nosaukums:</th>
	<td>
		<input type="text" name="data[poll_name]" value="{poll1_name}" size="48" />
	</td>
</tr>
<tr>
	<th align="right">Aktīvs?:</th>
	<td>
		<select name="data[poll_active]">
			<option value="Y"{poll1_active}>Jā</option>
			<option value="N"{poll1_inactive}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<th class="nowrap">Starta datums:</th>
	<td>
		<input type="text" name="data[poll_entered]" value="{poll1_entered}" />
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="button" value="Saglabāt" onclick="onSubmitHandler(this.form);" />
	</td>
</tr>
</table>
</form>
<script type="text/javascript">
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
<span><a href="#" onclick="javascript:history.back()">Atpakaļ</a></span>
<!-- END BLOCK_poll_error -->
<!-- BEGIN BLOCK_polldets --><!-- END BLOCK_polldets -->
<!-- BEGIN BLOCK_pollnew --><!-- END BLOCK_pollnew -->
