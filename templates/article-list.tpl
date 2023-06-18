<!-- BEGIN BLOCK_article -->
<div class="TD-cat">
	<div class="res-date">{res_date}</div>
	<div class="res-name"><a href="{res_route}">{res_name}</a></div>
	<div class="res-comments-link">
		<a href="{res_route}#art-comments-{doc_id}">Komentāri
		<span class="Comment-count {comment_class}">({res_comment_count})</span></a>
	</div>
</div>

<div class="Article-item">
	<div class="data">
		{res_intro}
		<!-- BEGIN BLOCK_art_cont disabled -->
		<div>
			<a href="{res_route}">..tālāk..</a>
		</div>
		<!-- END BLOCK_art_cont -->
	</div>
</div>
<!-- END BLOCK_article -->

<!-- BEGIN BLOCK_article_page disabled -->
<div class="TD-cat" style="display: flex;">
	<!-- BEGIN BLOCK_article_page_prev disabled -->
	<div>
		<img src="/img/left.png" alt="Vecāki ieraksti" style="vertical-align: middle;" width="16" height="16">
		<a class="caption" href="{page}">vecāki ieraksti</a>
	</div>
	<!-- END BLOCK_article_page_prev -->

	<!-- BEGIN BLOCK_article_page_next disabled -->
	<div style="margin-left: auto;">
		<a class="caption" href="{page}">jaunāki ieraksti</a>
		<img src="/img/right.png" alt="Jaunāki ieraksti" style="vertical-align: middle;" width="16" height="16">
	</div>
	<!-- END BLOCK_article_page_next -->
	<div>&nbsp;</div>
</div>
<!-- END BLOCK_article_page -->
