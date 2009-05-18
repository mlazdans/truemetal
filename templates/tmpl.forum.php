<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
	<tr>
		<td class="TD-cat" width="100%" nowrap>Diskusijas</td>
		<td class="TD-cat" nowrap>Tēmas</td>
	</tr>
	<!-- BEGIN BLOCK_forum disabled -->
	<tr>
		<td width="100%" nowrap class="TD-forum-cat"><a href="{module_root}/{forum_id}/" title="Datums: {forum_date}"><b>{forum_name}</b></a></td>
		<td class="TD-forum-cat"><!-- BEGIN BLOCK_comments_new disabled --><font color="red">{forum_childcount}</font><!-- END BLOCK_comments_new --><!-- BEGIN BLOCK_comments_old -->{forum_childcount}<!-- END BLOCK_comments_old --></td>
	</tr>
	<!-- BEGIN BLOCK_forum_data disabled -->
	<tr>
		<td colspan="2" width="100%" bgcolor="#444444">{forum_datacompiled}</td>
	</tr>
	<!-- END BLOCK_forum_data -->
	<tr>
		<td colspan="2" width="100%">&nbsp;</td>
	</tr>
	<!-- END BLOCK_forum -->
	<!-- BEGIN BLOCK_noforum disabled -->
	<tr>
		<td colspan="2" width="100%">Pagaidām nav neviena foruma!</td>
	</tr>
	<!-- END BLOCK_noforum  -->
</table>
