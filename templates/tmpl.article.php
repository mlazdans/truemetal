<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#444444">
<!-- BEGIN BLOCK_article disabled -->
<tr>
	<td width="100%">
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td align="right" class="TD-cat">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td class="TD-date">{art_date}</td>
							<td align="center" width="90%"><a href="{http_root}/article/{art_id}/" class="A-cat">{art_name}</a></td>
							<!-- BEGIN BLOCK_is_comments --><td align="right" nowrap><a href="{http_root}/article/{art_id}/#comments">Komentāri</a><span style="\{comment_style_{art_id}}">(\{comment_count_{art_id}})</span></td><!-- END BLOCK_is_comments -->
						</tr>
					</table>
				</td>
			</tr>
			<!-- BEGIN BLOCK_art_data -->
			<tr>
				<td width="100%">{art_data}</td>
			</tr>
			<!-- END BLOCK_art_data -->
			<!-- BEGIN BLOCK_art_cont disabled -->
			<tr>
				<td><a href="{http_root}/article/{art_id}/">..tālāk..</a></td>
			</tr>
			<!-- END BLOCK_art_cont -->
		</table>
	</td>
</tr>
<tr>
	<td width="100%" style="background-color: #660000"><img src="{http_root}/img/1x1.gif" alt="" border=""></td>
</tr>
<!-- END BLOCK_article  -->
<!-- BEGIN BLOCK_noarticle disabled -->
<tr>
	<td width="100%">-</td>
</tr>
<!-- END BLOCK_noarticle  -->

<!-- BEGIN BLOCK_article_page disabled -->
<tr>
	<td class="TD-cat">
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
		<tr>
			<!-- BEGIN BLOCK_article_page_prev disabled -->
			<td align="left"><a href="{page}" class="A-cat">&lt;&lt; vecāki ieraksti</a></td>
			<!-- END BLOCK_article_page_prev  -->

			<!-- BEGIN BLOCK_article_page_next disabled -->
			<td align="right"><a href="{page}" class="A-cat">jaunāki ieraksti &gt;&gt;</a></td>
			<!-- END BLOCK_article_page_next  -->
		</tr>
		</table>
	</td>
</tr>
<!-- END BLOCK_article_page -->

</table>

