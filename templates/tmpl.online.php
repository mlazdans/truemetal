<tr>
	<td class="TD-cat" title="Kas patlaban šeit ņemās?">Online [{online_total}]</td>
</tr>
<tr>
	<td bgcolor="#330000">
		<table cellpadding="0" cellspacing="1" border="0" width="100%">
			<!-- BEGIN BLOCK_online_item disabled -->
			<tr>
				<td width="100%" bgcolor="#660000"><a href="{http_root}/profile/user/{online_login_id}/" onclick="pop('{http_root}/profile/user/{online_login_id}/', 400, 400, 'profile{online_login_id}'); return false;">{online_name}</a></td>
			</tr>
			<!-- END BLOCK_online_item -->
			<!-- BEGIN BLOCK_online_item_notloged disabled -->
			<tr>
				<td width="100%" bgcolor="#660000">{online_name}</td>
			</tr>
			<!-- END BLOCK_online_item_notloged -->
			<!-- BEGIN BLOCK_online_anon disabled -->
			<tr>
				<td width="100%" bgcolor="#660000">&nbsp;</td>
			</tr>
			<tr>
				<td width="100%" bgcolor="#660000">{online_anon_count} {online_anon_name}</td>
			</tr>
			<!-- END BLOCK_online_anon -->
			<!-- BEGIN BLOCK_noonlines disabled -->
			<tr>
				<td width="100%" bgcolor="#660000">- Melnais Caurums -</td>
			</tr>
			<!-- END BLOCK_noonlines -->
		</table>
	</td>
</tr>
