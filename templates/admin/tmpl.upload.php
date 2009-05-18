<table cellpadding="2" cellspacing="2" border="0">
	<!-- BEGIN BLOCK_msg disabled -->
	<tr>
		<td colspan="2" class="error-msg">{msg}</td>
	</tr>
	<!-- END BLOCK_msg -->
	<form action="" method="post">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="file_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Faili</td>
	</tr>
<!-- BEGIN BLOCK_file_list disabled -->
	<!-- BEGIN BLOCK_file -->
	<tr>
		<td class="TD-cat"><input type="checkbox" name="file_{nr}" value="{file_name}"></td>
		<td><a href="{upload_root}/{file_name}" target="_blank">{file_name}</a></td>
	</tr>
	<!-- END BLOCK_file -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="delete_multiple">Dzēst</option>
		</select>
		<input type="submit" name="do_files" value="  OK  ">
		</td>
	</tr>
<!-- END BLOCK_file_list -->
	<input type="hidden" name="file_count" value="{nr}">
	</form>
<!-- BEGIN BLOCK_browse -->
	<tr>
		<td class="TD-cat"></td>
		<td class="TD-cat">Uzkopēt failu</td>
	</tr>
	<form action="" method="post" enctype="multipart/form-data">
	<tr>
		<td colspan="2">
		<input type="file" name="some_file">&nbsp;
		<input type="hidden" name="action" value="upload">
		<input type="submit" name="ok" value="  OK  ">
		</td>
	</tr>
	</form>
<!-- END BLOCK_browse -->
</table>
