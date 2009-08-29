<!-- BEGIN BLOCK_gallery_error disabled -->
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="error">{error_msg}</td>
</tr>
</table>
<!-- END BLOCK_gallery_error -->

<!-- BEGIN BLOCK_nogalleries disabled -->Diemžēl pagaidām šeit nav nevienas galerijas.<!-- END BLOCK_nogalleries -->
<!-- BEGIN BLOCK_gallery_list disabled -->
<table style="height: 100%;" cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#660000">
<tr>
	<td bgcolor="#330000" valign="top">
		<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<!-- BEGIN BLOCK_gallery -->
		<!-- BEGIN BLOCK_gallery_group_end disabled -->
		<tr>
			<td colspan="2" bgcolor="black" height="10"></td>
		</tr>
		<!-- END BLOCK_gallery_group_end -->

		<!-- BEGIN BLOCK_gallery_group disabled -->
		<tr>
			<td colspan="2" class="TD-cat"><a name="gg{gg_id}"></a>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td nowrap><b>{gg_name}</b></td>
					<td width="100%" align="right"><a href="#gg{gg_id}">[LINK]</a></td>
				</tr>
				</table>
			</td>
		</tr>
			<!-- BEGIN BLOCK_gallery_group_data disabled -->
			<tr>
				<td></td>
				<td bgcolor="#444444" style="border-bottom: 1px dotted #444444">{gg_data}</td>
			</tr>
			<!-- END BLOCK_gallery_group_data -->
		<!-- END BLOCK_gallery_group -->

		<tr>
			<!-- BEGIN BLOCK_gallery_padding disabled --><td style="padding-left: 10px"></td><!-- END BLOCK_gallery_padding -->
			<td<!-- BEGIN BLOCK_gallery_nogroup disabled --> class="TD-cat" colspan="2"<!-- END BLOCK_gallery_nogroup -->>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td nowrap><a name="gal{gal_id}"></a><a href="{module_root}/{gal_id}/">{gal_name}</a>: {gd_count} bildes</td>
					<td width="100%" align="right"><a href="#gal{gal_id}">[LINK]</a></td>
				</tr>
				</table>
			</td>
		</tr>

		<!-- BEGIN BLOCK_gallery_data disabled -->
		<tr>
			<!-- BEGIN BLOCK_gallery_data_padding disabled --><td style="padding-left: 10px"></td><!-- END BLOCK_gallery_data_padding -->
			<td<!-- BEGIN BLOCK_gallery_data_nogroup disabled -->  colspan="2"<!-- END BLOCK_gallery_data_nogroup --> bgcolor="#444444" style="border-bottom: 1px dotted #444444">{gal_data}</td>
		</tr>
		<!-- END BLOCK_gallery_data -->

	<!-- END BLOCK_gallery -->
		</table>
	</td>
</tr>
</table>
<!-- END BLOCK_gallery_list -->

<!-- BEGIN BLOCK_thumb_list disabled -->
<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
<tr>
	<td class="TD-cat" nowrap><a class="A-cat" href="{module_root}/#gal{gal_id}">Galerijas</a></td>
	<td class="TD-cat" nowrap>{gal_name}</td>
	<td class="TD-cat" width="100%">&nbsp;</td>
</tr>
</table>
<table cellspacing="18" cellpadding="0" border="0" align="center">
<!-- BEGIN BLOCK_thumb -->
<!-- BEGIN BLOCK_tr1 --><tr><!-- END BLOCK_tr1 -->
<td><a href="{module_root}/view/{gd_id}/"><img src="{http_root}/gal/{gd_galid}/{gd_id}.jpg" border="0" class="img-thumb"></a></td>
<!-- BEGIN BLOCK_tr2 --></tr><!-- END BLOCK_tr2 -->
<!-- END BLOCK_thumb -->
</table>
<!-- END BLOCK_thumb_list -->

<!-- BEGIN BLOCK_image disabled -->
<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
<tr>
	<td class="TD-cat" nowrap><a class="A-cat" href="{module_root}/#gal{gal_id}">Galerijas</a></td>
	<td class="TD-cat" nowrap><a class="A-cat" href="{module_root}/{gal_id}/">{gal_name}</a></td>
	<td class="TD-cat" width="100%">&nbsp;</td>
</tr>
</table>

<!-- BEGIN BLOCK_image_next disabled --><a href="{module_root}/view/{gd_nextid}">Nākamā</a><!-- END BLOCK_image_next -->

<!-- BEGIN BLOCK_image_viewsingle disabled -->
<p align="center"><img src="{module_root}/image/{gd_id}/" border="0" alt=""><br><br>{gd_descr}<br><br></p>
<!-- END BLOCK_image_viewsingle -->

<!-- BEGIN BLOCK_image_viewnext disabled -->
<p align="center"><a href="{module_root}/view/{gd_nextid}/"><img src="{module_root}/image/{gd_id}/" border="0" alt="Nākamā"></a><br><br>{gd_descr}<br><br></p>
<!-- END BLOCK_image_viewnext -->

<!-- END BLOCK_image -->


