<div style="margin-bottom: 2px;">
{article_page_top}
</div>

<!-- BEGIN BLOCK_article disabled -->
<div class="Article-item">
	<div class="TD-cat">
		<div class="date">{art_date}</div>
		<div class="comments">
			<a href="{http_root}/{module_id}/{art_id}/#comments">Komentāri</a>
			<span<!-- BEGIN BLOCK_comments_new disabled --> class="Comment-new"<!-- END BLOCK_comments_new -->>({art_comment_count})</span>
		</div>
		<div class="caption"><a class="caption" href="{http_root}/{module_id}/{art_id}-{art_name_url}">{art_name}</a></div>
	</div>

	<!-- BEGIN BLOCK_art_intro -->
	<div class="data">
		{art_intro}
		<!-- BEGIN BLOCK_art_cont disabled -->
		<div>
			<a href="{http_root}/{module_id}/{art_id}-{art_name_url}">..tālāk..</a>
		</div>
		<!-- END BLOCK_art_cont -->
		<div class="clear"></div>
	</div>
	<!-- END BLOCK_art_intro -->

	<!-- BEGIN BLOCK_art_data disabled -->
	<div class="data">
		<div class="intro">
		{art_intro}
		</div>
		{art_data}
	</div>
	<!-- END BLOCK_art_data -->

	<!-- BEGIN BLOCK_art_date_formatted disabled -->
	<div class="List-item">
		Ievietots: {art_date_f}
	</div>
	<!-- END BLOCK_art_date_formatted -->
</div>
<!-- END BLOCK_article  -->

<!-- BEGIN BLOCK_noarticle disabled -->
<div class="TD-cat">
	Not found
</div>
<div class="Info">
	Resurss nav atrasts
</div>
<!-- END BLOCK_noarticle  -->

<!-- BEGIN BLOCK_article_comments_head disabled -->
<div class="TD-cat">
	Komentāri
</div>
<!-- END BLOCK_article_comments_head -->

<!-- BEGIN BLOCK_article_comments --><!-- END BLOCK_article_comments -->

<!-- BEGIN BLOCK_article_page disabled -->
<div class="TD-cat">
	<!-- BEGIN BLOCK_article_page_prev disabled -->
	<div style="float: left;">
		<img src="{http_root}/img/left.png" alt="Vecāki ieraksti" style="vertical-align: middle;" />
		<a class="caption" href="{page}">vecāki ieraksti</a>
	</div>
	<!-- END BLOCK_article_page_prev  -->

	<!-- BEGIN BLOCK_article_page_next disabled -->
	<div style="float: right;">
		<a class="caption" href="{page}">jaunāki ieraksti</a>
		<img src="{http_root}/img/right.png" alt="Jaunāki ieraksti" style="vertical-align: middle;" />
	</div>
	<!-- END BLOCK_article_page_next -->
	<div>&nbsp;</div>
</div>
<!-- END BLOCK_article_page -->

