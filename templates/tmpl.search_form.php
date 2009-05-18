<tr>
	<td class="TD-cat" title="Meklētājs">Meklētājs</td>
</tr>
<tr>
	<td bgcolor="#330000">
		<form
			id="search_form"
			method="POST"
			action="{http_root}/search/"
			onsubmit="$('#search_form').attr('action', '{http_root}/search/?search_q=' + $('#search_form_search_q').val()); $('#search_sPPAAMMcheck').val(0);"
		>
		<input id="search_sPPAAMMcheck" type="hidden" name="spam" value="1">
		<table cellpadding="0" cellspacing="1" border="0" width="100%">
		<tr>
			<td bgcolor="#660000"><input type="text" name="search_q" id="search_form_search_q" class="input" style="width: 129px" value="{search_q}"></td>
			<td bgcolor="#660000"><input type="submit" class="input" value=" OK "></td>
		</tr>
		<tr>
			<td colspan="2" bgcolor="#660000" align="center"><a href="{http_root}/search_log/">Ko mēs meklējam?</a></td>
		</tr>
		</table>
		</form>
	</td>
</tr>

