<!-- BEGIN BLOCK_login_form disabled -->
<div class="TD-cat" title="Pieslēgties sistēmai">
	Pieslēgties
</div>

<div class="List-item">
<form action="{http_root}/login/" method="post">
<table cellpadding="0" cellspacing="1" border="0" width="100%">
<tr>
	<td colspan="2">
		<input type="hidden" name="data[referer]" value="{referer}" />
		<input style="width: 158px;" type="text" name="data[login]" value="Lietotāja vārds" onfocus="if(this.value=='Lietotāja vārds')this.value=''" />
	</td>
</tr>
<tr>
	<td><input style="width: 120px;" type="password" name="data[password]" size="13" /></td>
	<td><input type="submit" class="input" value=" OK " /></td>
</tr>
</table>
</form>
</div>

<div class="List-item">
	<a href="{http_root}/register/">Reģistrācija</a>
</div>
<div class="List-item">
	<a href="{http_root}/forgot/">Aizmirsu paroli</a>
</div>
<!-- END BLOCK_login_form -->

<!-- BEGIN BLOCK_login_data disabled -->
<div class="TD-cat" title="Pieslēgties sistēmai">
	Login
</div>
<div class="List-item">
	{login_nick}
</div>
<div class="List-item">
	<a href="/login/logoff/" onclick="return confirm('Tu ko?! Nezini, kas ir Amorphis???');">Log Off</a>
</div>
<div class="List-item">
	<a href="{http_root}/profile/" title="Lietotāja profils">Tavs profils</a>
</div>
<div class="List-item">
	<a href="{http_root}/mark/" title="Iezīmēt visus komentārus kā izlasītus">Mark all as read</a>
</div>
<!-- END BLOCK_login_data -->

