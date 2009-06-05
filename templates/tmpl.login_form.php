<!-- BEGIN BLOCK_login_form disabled -->
<tr>
	<td class="TD-cat" title="Pieslēties sistēmai">Pieslēgties</td>
</tr>
<tr>
	<td bgcolor="#330000" align="center">
		<form action="{http_root}/login/" method="post" name="login_form">
		<input type="hidden" name="data[referer]" value="{referer}">
		<table cellpadding="0" cellspacing="1" border="0" align="center">
		<tr>
			<td><input type="text" name="data[login]" class="input" style="width: 100%" value="Lietotāja vārds" onFocus="this.value=''"></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td><input type="password" style="width: 129px" name="data[password]" class="input" size="13"></td>
					<td>&nbsp;</td>
					<td><input type="submit" class="input" value=" OK "></td>
				</tr>
				<tr>
					<td colspan="3" align="center"><a href="{http_root}/register/">Reģistrācija</a></td>
				</tr>
				<tr>
					<td colspan="3" align="center"><a href="{http_root}/forgot/">Aizmirsu paroli</a></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
<!-- END BLOCK_login_form -->
<!-- BEGIN BLOCK_login_data disabled -->
<tr>
	<td class="TD-cat" title="Pieslēties sistēmai">Login</td>
</tr>
<tr>
	<td>
		<table cellpadding="0" cellspacing="1" border="0">
		<tr><td>{login_nick}</td></tr>
		<tr><td><a href="javascript:checkLogOff()">Log Off</a></td></tr>
		<tr><td><a href="{http_root}/profile/" title="Lietotāja profils">Tavs profils</a></td></tr>
		<tr><td><a href="{http_root}/mark/" title="Iezīmēt visus komentārus kā izlasītus">Mark all as read</a></td></tr>
		</table>
	</td>
</tr>
<!-- END BLOCK_login_data -->
