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
	<div class="TD-cat">
		{gg_name}
	</div>
	<div style="padding-left: 20px;">{gg_data}</div>
	<!-- END BLOCK_gallery_group -->

	<!-- BEGIN BLOCK_gallery_data -->
	<div id="gal{gal_id}" style="padding-left: 20px;">
		<a href="{module_root}/{gal_id}/">{gal_name}</a>
	</div>
	<!-- END BLOCK_gallery_data -->
</div>
<!-- END BLOCK_gallery_list -->

<!-- BEGIN BLOCK_thumb_list disabled -->
<div class="TD-cat">
	<a href="{module_root}/#gal{gal_id}">Galerijas</a> / {gal_name}
</div>
	<!-- BEGIN BLOCK_thumb -->
		<!-- BEGIN BLOCK_tr1 --><div style="text-align: center;"><!-- END BLOCK_tr1 -->
		<a href="{module_root}/view/{gd_id}/#pic"><img src="{thumb_path}" alt="" class="img-thumb" /></a>
		<!-- BEGIN BLOCK_tr2 --></div><!-- END BLOCK_tr2 -->
	<!-- END BLOCK_thumb -->
<!-- END BLOCK_thumb_list -->

<!-- BEGIN BLOCK_image disabled -->
<div class="TD-cat">
	<a class="A-cat" href="{module_root}/#gal{gal_id}">Galerijas</a> /
	<a class="A-cat" href="{module_root}/{gal_id}/">{gal_name}</a>
</div>
	<!-- BEGIN BLOCK_image_viewsingle disabled -->
	<div style="text-align: center;"><img src="{module_root}/image/{gd_id}/" alt="" /></div>
	<div style="text-align: center;">{gd_descr}</div>
	<!-- END BLOCK_image_viewsingle -->

	<!-- BEGIN BLOCK_image_viewnext disabled -->
	<div style="text-align: center;"><a name="pic" href="{module_root}/view/{gd_nextid}/#pic"><img src="{module_root}/image/{gd_id}/" alt="Nākamā" /></a></div>
	<div style="text-align: center;">{gd_descr}</div>
	<!-- END BLOCK_image_viewnext -->
<!-- END BLOCK_image -->

