<!-- BEGIN BLOCK_login_form disabled -->
<div class="List-item">
<form action="/login/" method="post">
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td colspan="2">
		<input type="hidden" name="data[referer]" value="{referer}">
		<input style="width: 100%;" type="text" name="data[login]" placeholder="Logins / e-pasts">
	</td>
</tr>
<tr>
	<td><input style="width: 100%;" type="password" name="data[password]" size="13" placeholder="Parole"></td>
	<td style="text-align: center;"><input type="submit" class="input" value=" OK "></td>
</tr>
</table>
</form>
</div>

<div class="List-item">
	<a href="/register/">Reģistrācija</a>
</div>
<div class="List-item">
	<a href="/forgot/">Aizmirsu paroli</a>
</div>
<!-- END BLOCK_login_form -->

<!-- BEGIN BLOCK_login_data disabled -->
<div class="List-item">
	{login_nick}
</div>
<div class="List-item">
	<a href="/login/logoff/" onclick="return confirm('Tu ko?! Nezini, kas ir Amorphis???');">Log Off</a>
</div>
<div class="List-item">
	<a href="/user/profile/" title="Lietotāja profils">Tavs profils</a>
</div>
<!-- END BLOCK_login_data -->
