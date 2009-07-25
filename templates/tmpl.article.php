<!-- BEGIN BLOCK_article disabled -->
<div class="Article-item">
	<div class="TD-cat">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="TD-date">{art_date}</td>
				<td align="center" width="100%"><a href="{http_root}/article/{art_id}/" class="A-cat">{art_name}</a></td>
				<!-- BEGIN BLOCK_is_comments --><td align="right" nowrap><a href="{http_root}/article/{art_id}/#comments">Komentāri</a><span style="\{comment_style_{art_id}}">(\{comment_count_{art_id}})</span></td><!-- END BLOCK_is_comments -->
			</tr>
		</table>
	</div>

	<!-- BEGIN BLOCK_art_data -->
	<div class="data">{art_data}</div>
	<!-- END BLOCK_art_data -->

	<!-- BEGIN BLOCK_art_cont disabled -->
	<div><a href="{http_root}/article/{art_id}/">..tālāk..</a></div>
	<!-- END BLOCK_art_cont -->
</div>
<!--
<div style="background-color: #660000"><img src="{http_root}/img/1x1.gif" alt="" border=""></div>
-->
<!-- END BLOCK_article  -->

<!-- BEGIN BLOCK_noarticle disabled -->
<div>-</div>
<!-- END BLOCK_noarticle  -->

<!-- BEGIN BLOCK_article_page disabled -->
<div class="TD-cat">
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
</div>
<!-- END BLOCK_article_page -->

