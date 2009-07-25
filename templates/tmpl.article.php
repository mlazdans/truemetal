<!-- BEGIN BLOCK_article disabled -->
<div class="Article-item">
	<div class="TD-cat">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="TD-date">{art_date}</td>
				<td align="center" style="width: 100%;"><a href="{http_root}/article/{art_id}/" class="A-cat">{art_name}</a></td>
				<!-- BEGIN BLOCK_is_comments -->
				<td align="right" style="white-space: nowrap;">
					<a href="{http_root}/article/{art_id}/#comments">Komentāri</a><span style="\{comment_style_{art_id}}">(\{comment_count_{art_id}})</span>
				</td>
				<!-- END BLOCK_is_comments -->
			</tr>
		</table>
	</div>

	<!-- BEGIN BLOCK_art_data -->
	<div class="data">
		{art_data}
		<!-- BEGIN BLOCK_art_cont disabled -->
		<div>
			<a href="{http_root}/article/{art_id}/">..tālāk..</a>
		</div>
		<!-- END BLOCK_art_cont -->
	</div>
	<!-- END BLOCK_art_data -->

	<!-- BEGIN BLOCK_art_data_formatted disabled -->
	<div class="List-item">
		Ievietots: {art_date_f}
	</div>
	<!-- END BLOCK_art_data_formatted -->
</div>
<!-- END BLOCK_article  -->

<!-- BEGIN BLOCK_noarticle disabled -->
<div>Not found</div>
<!-- END BLOCK_noarticle  -->

<!-- BEGIN BLOCK_article_comments disabled -->
Comments
<!-- END BLOCK_article_comments -->


<!-- BEGIN BLOCK_article_page disabled -->
<div class="TD-cat">
	<!-- BEGIN BLOCK_article_page_prev disabled -->
	<div style="float: left; position: relative;">
		<a href="{page}" class="A-cat">&lt;&lt; vecāki ieraksti</a>
	</div>
	<!-- END BLOCK_article_page_prev  -->

	<!-- BEGIN BLOCK_article_page_next disabled -->
	<div style="float: right; position: relative;">
		<a href="{page}" class="A-cat">jaunāki ieraksti &gt;&gt;</a>
	</div>
	<!-- END BLOCK_article_page_next -->
</div>
<!-- END BLOCK_article_page -->

