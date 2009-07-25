<div class="TD-cat" title="Ikdienišķais jautājums">
	Jautājums
</div>

<div class="List-item">
<table cellpadding="0" cellspacing="0">
<tr>
	<td colspan="2"><b>{poll_question}</b></td>
</tr>
<!-- BEGIN BLOCK_poll_items disabled -->
<tr>
	<td colspan="2" style="height: 5px;"></td>
</tr>
<tr>
	<td style="border: 1px dotted red; width: 100%;">{poll_name}</td>
	<td style="border: 1px dotted red;">{count_votes}</td>
</tr>
<tr>
	<td><!-- BEGIN BLOCK_poll_bar --><img src="{http_root}/img/1x1pollchart.gif" width="{poll_width}" height="10" alt="0" /><!-- END BLOCK_poll_bar --></td>
	<td>{count_percent}%</td>
</tr>
<!-- END BLOCK_poll_items -->
<tr>
	<td align="right"><b>Kopā:</b></td>
	<td><b>{total_votes}</b></td>
</tr>
</table>
</div>

<div class="List-item">
	<a href="{http_root}/poll/results/">Jautājumu arhīvs</a>
</div>

