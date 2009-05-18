<!-- BEGIN BLOCK_poll_error disabled -->
<table>
<tr>
	<td class="error">{error_msg}</td>
</tr>
</table>
<!-- END BLOCK_poll_error -->

<!-- BEGIN BLOCK_poll disabled -->
<tr>
	<td class="TD-cat">Jautājums</td>
</tr>
<tr>
	<td bgcolor="#330000">
		<form action="{http_root}/poll/vote/" method="post" name="vote_form">
		<input type="hidden" name="poll_id" value="{poll_question_id}">
		<table cellpadding="0" cellspacing="1" border="0" width="100%">
			<tr>
				<td colspan="2" bgcolor="#660000"><b>{poll_question}</b></td>
			</tr>
			<!-- BEGIN BLOCK_poll_items disabled -->
			<tr>
				<td bgcolor="#660000"><input type="radio" class="cbox" value="{poll_id}" name="poll_pollid" id="pv{poll_id}"></td>
				<td width="100%" bgcolor="#660000"><label for="pv{poll_id}">{poll_name}</td>
			</tr>
			<!-- END BLOCK_poll_items -->
			<tr>
				<td align="right" colspan="2"><input type="submit" value="Balsot">&nbsp;<input type="button" value="Rezultāti" onClick="location.replace('{http_root}/poll/results/')"></td>
			</tr>
		</table>
		</form>
	</td>
</tr>
<!-- END BLOCK_poll -->