<form action="" method="post">
<input type="hidden" name="action" value="gg_new">
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td valign="top"><input type="submit" name="do" value="Jauns"></td>
		<td valign="top"><input type="button" value="Atcelt" onClick="location.replace('{http_root}/admin/gallery_group/')"></td>
		<td valign="top" width="100%">&nbsp;</td>
	</tr>
</table>
</form>

<!-- BEGIN BLOCK_gallery_group_list disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="gallery_group_check_all" onClick="Truemetal.checkAll(this)"></td>
		<td width="100%" class="TD-cat">Galeriju grupas</td>
	</tr>
	<!-- BEGIN BLOCK_gallery_groups -->
	<input type="hidden" name="gg_id{gallery_group_nr}" value="{gg_id}">
	<tr>
		<td class="{gg_color_class}"><input type="checkbox" name="gg_checked{gallery_group_nr}"></td>
		<td class="{gg_color_class}" valign="top"><a href="{module_root}/{gg_id}/">{gg_name}</a></td>
	</tr>
	<!-- END BLOCK_gallery_groups -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		</select>
		<input type="submit" value="  OK  ">
		</td>
	</tr>
</table>
<input type="hidden" name="gallery_group_count" value="{gallery_group_count}">
</form>
<!-- END BLOCK_gallery_group_list -->

<!-- BEGIN BLOCK_gallery_group_error disabled -->
<span class="error-msg">{error_msg}</span>
<br><a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_gallery_group_error -->

<!-- BEGIN BLOCK_gallery_group_edit disabled -->
<form action="{module_root}/{gg_id}/save" method="post" name="{editor_id}">
<input type="hidden" name="action" value="gg_save">
<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
		<td>Nosaukums</th><td width="100%"><input type="text" name="data[gg_name]" value="{gg_name}" size="48"></td>
	</tr>
	<tr>
		<td nowrap>Pasākuma datums</th><td width="100%"><input type="text" name="data[gg_date]" value="{gg_date}" size="20"></td>
	</tr>
	<tr>
		<td nowrap>Ievadīšanas datums</th><td width="100%"><input type="text" name="data[gg_entered]" value="{gg_entered}" size="20"></td>
	</tr>
	<tr>
		<td colspan="2">Apraksts:</td>
	</tr>
	<tr>
		<td colspan="2" width="100%"><!-- BEGIN BLOCK_editor --><!-- END BLOCK_editor --></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" value="Saglabāt"></td>
	</tr>
</table>
</form>
<!-- END BLOCK_gallery_group_edit -->
