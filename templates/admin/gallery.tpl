<form action="" method="post">
<input type="hidden" name="action" value="gal_new">
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top"><input type="submit" name="do" value="Jauns"></td>
		<td valign="top"><input type="button" value="Atcelt" onClick="location.replace('{http_root}/admin/gallery/')"></td>
		<td valign="top" width="100%">&nbsp;</td>
	</tr>
</table>
</form>

<!-- BEGIN BLOCK_gallery_list disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="gallery_check_all" onClick="Truemetal.checkAll(this)"></td>
		<td colspan="2" width="100%" class="TD-cat">Galerijas</td>
	</tr>
	<!-- BEGIN BLOCK_galleries -->
	<input type="hidden" name="gal_id{gallery_nr}" value="{gal_id}">
		<!-- BEGIN BLOCK_gallery_group disabled -->
	<tr>
		<td class="TD-cat" colspan="3"><a href="{admin_root}/gallery_group/{gal_ggid}/" class="A-cat">{gg_name}</a></td>
	</tr>
		<!-- END BLOCK_gallery_group -->
	<tr>
		<!-- BEGIN BLOCK_gallery_padding disabled --><td class="{gal_color_class}">&nbsp;</td><!-- END BLOCK_gallery_padding -->
		<td class="{gal_color_class}"><input type="checkbox" name="gal_checked{gallery_nr}"></td>
		<td<!-- BEGIN BLOCK_gallery_nopadding disabled --> colspan="2"<!-- END BLOCK_gallery_nopadding --> class="{gal_color_class}" width="100%"><a href="{module_root}/{gal_id}/">{gal_name}</a></td>
	</tr>
	<!-- END BLOCK_galleries -->
	<tr>
		<td colspan="3">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		<option value="show_multiple">Parādīt</option>
		<option value="hide_multiple">Slēpt</option>
		</select>
		<input type="submit" value="  OK  ">
		</td>
	</tr>
</table>
<input type="hidden" name="gallery_count" value="{gallery_count}">
</form>
<!-- END BLOCK_gallery_list -->

<!-- BEGIN BLOCK_gallery_error disabled -->
<span class="error-msg">{error_msg}</span>
<br><a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_gallery_error -->

<!-- BEGIN BLOCK_gallery_edit disabled -->
<form action="{module_root}/{gal_id}/save" method="post" name="{editor_id}">
<input type="hidden" name="action" value="gal_save">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
		<td>Nosaukums</th><td width="100%"><input type="text" name="data[gal_name]" value="{gal_name}" size="48"></td>
	</tr>
	<tr>
		<td>Grupa</th><td><select name="data[gal_ggid]">
		<option value="0">-</option>
		<!-- BEGIN BLOCK_gallery_groups -->
		<option value="{gg_id}"{gg_selected}>{gg_name}</option>
		<!-- END BLOCK_gallery_groups -->
		</select></td>
	</tr>
	<tr>
		<td>Aktīvs?</th><td><select name="data[gal_active]">
		<option value="Y"{gal_active_y}>Jā</option>
		<option value="N"{gal_active_n}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td>Redzams?</th><td><select name="data[gal_visible]">
		<option value="Y"{gal_visible_y}>Jā</option>
		<option value="N"{gal_visible_n}>Nē</option>
		</select></td>
	</tr>
	<tr>
		<td nowrap>Ievadīšanas datums</th><td width="100%"><input type="text" name="data[gal_entered]" value="{gal_entered}" size="20"></td>
	</tr>
	<!-- BEGIN BLOCK_gallery_submit disabled -->
	<tr>
		<td colspan="2"><input type="submit" value="Saglabāt"></td>
	</tr>
	<!-- END BLOCK_gallery_submit -->
	<tr>
		<td colspan="2" width="100%"><!-- BEGIN BLOCK_editor --><!-- END BLOCK_editor --></td>
	</tr>
</table>
<!-- BEGIN BLOCK_gallery_data --><!-- END BLOCK_gallery_data -->
</form>
<!-- END BLOCK_gallery_edit -->

