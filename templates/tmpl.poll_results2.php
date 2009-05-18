<hr>
<table width="50%" cellpadding="0" cellspacing="1" border="0" align="center" bgcolor="#330000">
<tr>
	<td colspan="2"><b>{poll_question_b}</b></td>
</tr>
<!-- BEGIN BLOCK_poll_r_items disabled -->
<tr>
	<td colspan="2" height="5"></td>
</tr>
<tr>
	<td colspan="2">{poll_name_b}</td>
</tr>
<tr>
	<td width="100%" bgcolor="#660000"><!-- BEGIN BLOCK_poll_r_bar --><img src="{http_root}/img/1x1pollchart.gif" style="width: {count_percent_b}%" height="10" alt="0"><!-- END BLOCK_poll_r_bar --></td>
	<td nowrap>{count_votes_b} ({count_percent_b}%)</td>
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
<hr>
<table cellpadding="0" cellspacing="1" border="0">
<!-- BEGIN BLOCK_poll_archive_item -->
<tr>
	<td><a href="{module_root}/results/{poll_id_a}/">{poll_name_a}</a></td>
</tr>
<!-- END BLOCK_poll_archive_item -->
</table>

<!-- END BLOCK_poll_archive -->

