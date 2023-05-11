<form
	id="search_form"
	method="post"
	action="/search/"
	onsubmit="$('#search_form').attr('action', '/search/?search_q=' + $('#search_form_search_q').val()); $('#search_sPPAAMMcheck').val(0);"
>
<input id="search_sPPAAMMcheck" type="hidden" name="spam" value="1">
<div class="List-item"><input type="text" name="search_q" id="search_form_search_q" class="input" style="width: 100%;" value="{search_q}"></div>
<div class="List-item"><input type="submit" class="input" value="OK"></div>
</form>

<div class="List-item">
	<a href="/search/">Advanced</a>
</div>

<div class="List-item">
	<a href="/search_log/">Ko mēs meklējam?</a>
</div>
