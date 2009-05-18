<tr>
	<td class="TD-cat">Jautājums</td>
</tr>
<tr>
	<td bgcolor="#330000">
		<table cellpadding="0" cellspacing="1" border="0" width="100%">
			<tr>
				<td colspan="2" bgcolor="#660000"><b>{poll_question}</b></td>
			</tr>
			<!-- BEGIN BLOCK_poll_items disabled -->
			<tr>
				<td colspan="2" height="5"></td>
			</tr>
			<tr>
				<td style="border-top: 1px dotted red" width="100%" bgcolor="#660000">{poll_name}</td>
				<td style="border-top: 1px dotted red" bgcolor="#660000">{count_votes}</td>
			</tr>
			<tr>
				<td bgcolor="#660000"><!-- BEGIN BLOCK_poll_bar --><img src="{http_root}/img/1x1pollchart.gif" width="{poll_width}" height="10" alt="0"><!-- END BLOCK_poll_bar --></td>
				<td bgcolor="#660000">{count_percent}%</td>
			</tr>
			<!-- END BLOCK_poll_items -->
			<tr>
				<td align="right"><b>Kopā:</b></td>
				<td><b>{total_votes}</b></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="10"></td>
</tr>
<tr>
	<td align="center"><a href="{http_root}/poll/results/">Jautājumu arhīvs</a></td>
</tr>
