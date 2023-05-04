<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="online_check_all" onClick="Truemetal.checkAll(this)"></td>
		<td width="100%" class="TD-cat">Online</td>
	</tr>
	<!-- BEGIN BLOCK_online_item -->
	<tr>
		<td class="box-normal"><input type="checkbox" name="online_checked{ub_nr}"></td>
		<td class="box-normal" valign="top">{online_name} ({online_ip})</td>
	</tr>
	<!-- END BLOCK_online_item -->
	<!-- BEGIN BLOCK_noonlines disabled -->
	<tr>
		<td valign="top">Pašlaik neviens lapu neskatās!</td>
	</tr>
	<!-- END BLOCK_noonlines -->
</table>