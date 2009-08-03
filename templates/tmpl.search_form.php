<div class="TD-cat" title="Meklētājs">
	Meklētājs
</div>

<div class="List-item">
<form
	id="search_form"
	method="post"
	action="{http_root}/search/"
	onsubmit="$('#search_form').attr('action', '{http_root}/search/?search_q=' + $('#search_form_search_q').val()); $('#search_sPPAAMMcheck').val(0);"
>
<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr>
	<td>
		<input id="search_sPPAAMMcheck" type="hidden" name="spam" value="1" />
		<input type="text" name="search_q" id="search_form_search_q" class="input" style="width: 113px;" value="{search_q}" />
	</td>
	<td>
		<input type="submit" class="input" value="OK" />
	</td>
</tr>
</table>
</form>
</div>

<div class="List-item">
	<a href="{http_root}/search/">Advanced</a>
</div>

<div class="List-item">
	<a href="{http_root}/search_log/">Ko mēs meklējam?</a>
</div>

