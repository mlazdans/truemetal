<!-- BEGIN BLOCK_poll_error disabled -->
<table>
<tr>
	<td class="error">{error_msg}</td>
</tr>
</table>
<!-- END BLOCK_poll_error -->

<!-- BEGIN BLOCK_poll disabled -->
<div class="TD-cat" title="Ikdienišķais jautājums">
	Jautājums
</div>
<div class="List-item">
	<form action="/poll/vote/" method="post" id="poll_vote_form">
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">
			<input type="hidden" name="poll_id" value="{poll_question_id}">
			<b>{poll_question}</b>
		</td>
	</tr>
	<!-- BEGIN BLOCK_poll_items disabled -->
	<tr>
		<td>
			<input type="radio" class="checkbox" value="{poll_id}" name="poll_pollid" id="pv{poll_id}">
		</td>
		<td style="border: 1px dotted red; width: 100%;">
			<label for="pv{poll_id}">{poll_name}</label>
		</td>
	</tr>
	<!-- END BLOCK_poll_items -->
	<tr>
		<td colspan="2" style="padding-top: 1em;">
			<input type="submit" value="Balsot">
			<input type="button" value="Rezultāti" onclick="location.href='/poll/results/';">
		</td>
	</tr>
	</table>
	</form>
</div>
<div class="List-item">
	<a href="/poll/results/">Jautājumu arhīvs</a>
</div>
<!-- END BLOCK_poll -->
