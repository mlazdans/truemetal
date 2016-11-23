<!-- BEGIN BLOCK_gallery_error disabled -->
<div class="Info">
	{error_msg}
</div>
<!-- END BLOCK_gallery_error -->

<!-- BEGIN BLOCK_nogalleries disabled -->
<div class="Info">
	Diemžēl pagaidām šeit nav nevienas galerijas.
</div>
<!-- END BLOCK_nogalleries -->

<!-- BEGIN BLOCK_gallery_list disabled -->
<div style="margin-bottom: 20px;">
	<!-- BEGIN BLOCK_gallery_group -->
	<div id="{gal_jump_id}" class="TD-cat">
		{gg_name}
	</div>
	<div style="padding-left: 20px;">{gg_data}</div>
	<!-- END BLOCK_gallery_group -->

	<!-- BEGIN BLOCK_gallery_data -->
	<div id="gal{gal_id}" style="padding-left: 20px;">
		<a href="{module_root}/{gal_id}/">{gal_name}</a> {gal_data}
	</div>
	<!-- END BLOCK_gallery_data -->
</div>
<!-- END BLOCK_gallery_list -->

<!-- BEGIN BLOCK_thumb_list disabled -->
<div class="TD-cat">
	<a href="{module_root}/#{gal_jump_id}">Galerijas</a> / {gal_name}
</div>
	<!-- BEGIN BLOCK_thumb -->
		<!-- BEGIN BLOCK_tr1 --><div style="text-align: center;"><!-- END BLOCK_tr1 -->
		<a href="{module_root}/view/{gd_id}/#pic-holder"><img src="{thumb_path}" alt="" class="img-thumb" /></a>
		<!-- BEGIN BLOCK_tr2 --></div><!-- END BLOCK_tr2 -->
	<!-- END BLOCK_thumb -->
<!-- END BLOCK_thumb_list -->

<!-- BEGIN BLOCK_image disabled -->
<div class="TD-cat" id="pic-holder">
	<a class="A-cat" href="{module_root}/#{gal_jump_id}">Galerijas</a> /
	<a class="A-cat" href="{module_root}/{gal_id}/">{gal_name}</a>
</div>

<div class="Comment" id="gal_id{gal_id}" style="margin: 0;">
	<div class="header">
		<div class="vote unselectable">
			<a href="{http_root}/vote/down/{res_id}/" title="Dauns..." onclick="Truemetal.Vote('{res_id}', 'down', '#gal_id{gal_id} .vote-value'); return false;">[&ndash;]</a>
		</div>
		<div class="vote unselectable">
			<a href="{http_root}/vote/up/{res_id}/" title="Ōjā!" onclick="Truemetal.Vote('{res_id}', 'up', '#gal_id{gal_id} .vote-value'); return false;">[+]</a>
		</div>
		<div class="vote {comment_vote_class} vote-value unselectable">
			{res_votes}
		</div>
		<div class="center unselectable">&nbsp;</div>
	</div>
</div>
<div style="text-align: center;"><a href="{module_root}/view/{gd_nextid}/#pic-holder"><img src="{image_path}" alt="Nākamā" /></a></div>
<div style="text-align: center;">{gd_descr}</div>

<div class="TD-cat">
	Komentāri
</div>
<!-- BEGIN BLOCK_gallery_comments --><!-- END BLOCK_gallery_comments -->

<!-- END BLOCK_image -->

