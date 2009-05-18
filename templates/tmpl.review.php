<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#444444">
	<!-- BEGIN BLOCK_review disabled -->
	<tr>
		<td width="100%" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="right" class="TD-cat">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td class="TD-date">{rev_date}</td>
								<td align="center" width="50%"><a href="{http_root}/reviews/{rev_id}/" class="A-cat">{rev_bandname} - {rev_cdname}</a></td>
								<td align="right" nowrap><a href="{http_root}/reviews/{rev_id}/#comments">Komentāri</a> <!-- BEGIN BLOCK_comments_new disabled --><font color="red">({comment_count})</font><!-- END BLOCK_comments_new --><!-- BEGIN BLOCK_comments_old -->({comment_count})<!-- END BLOCK_comments_old --></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="100%" valign="top">
						<table width="100%" cellpadding="2" cellspacing="1" border="0">
							<tr><td rowspan="5" width="100" bgcolor="#660000"><img src="{rev_cdimage}"></td><td bgcolor="#660000">{rev_bandname} - {rev_cdname}</td></tr>
							<tr><td bgcolor="#660000">Gads: {rev_cdyear}</td></tr>
							<tr><td bgcolor="#660000">Vērtējums: {rev_cdrating}</td></tr>
							<tr><td bgcolor="#660000">Mājaslapa: <a href="{rev_bandhomepage}">{rev_bandhomepage}</a></td></tr>
							<tr><td bgcolor="#660000">Autors: {rev_username}</td></tr>
							<tr><td colspan="2">{rev_data}</td></tr>
						</table>
					</td>
				</tr>
				<!-- BEGIN BLOCK_rev_cont disabled -->
				<tr><td><a href="{http_root}/reviews/{rev_id}/">..tālāk..</a></td></tr>
				<!-- END BLOCK_rev_cont -->
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%"><img src="{http_root}/img/1x1.gif" alt="" border="" height="15"></td>
	</tr>
	<!-- END BLOCK_review  -->
	<tr><td height="100%"></td></tr>
	<!-- BEGIN BLOCK_noreview disabled -->
	<tr>
		<td width="100%">-</td>
	</tr>
	<!-- END BLOCK_noreview  -->
</table>
