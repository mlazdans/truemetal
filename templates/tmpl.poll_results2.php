<div class="TD-cat" title="Atbildes jautājumam: {poll_question_b}">
	{poll_question_b}
</div>

<table width="95%" cellpadding="0" cellspacing="2" border="0" align="center" bgcolor="#330000">
<!-- BEGIN BLOCK_poll_r_items disabled -->
<tr>
	<td colspan="2">{poll_name_b}</td>
</tr>
<tr>
	<td width="100%" bgcolor="#660000"><!-- BEGIN BLOCK_poll_r_bar --><img src="{http_root}/img/1x1pollchart.gif" style="width: {count_percent_b}%" height="10" alt="0" /><!-- END BLOCK_poll_r_bar --></td>
	<td nowrap>{count_votes_b} ({count_percent_b}%)</td>
</tr>
<tr>
	<td colspan="2" height="15"></td>
</tr>
<!-- END BLOCK_poll_r_items -->
<tr>
	<td style="text-align: right;">Kopā:</td>
	<td><b>{total_votes_b}</b></td>
</tr>
<tr>
	<td colspan="2" style="padding-bottom: 7px">&nbsp;</td>
</tr>
</table>

<!-- BEGIN BLOCK_poll_archive disabled -->
<div class="TD-cat">
	Pārējie jautājumi
</div>

<!-- BEGIN BLOCK_poll_archive_item -->
<div class="List-item">
	<a href="{module_root}/results/{poll_id_a}/">{poll_name_a}</a>
</div>
<!-- END BLOCK_poll_archive_item -->
<!-- END BLOCK_poll_archive -->

