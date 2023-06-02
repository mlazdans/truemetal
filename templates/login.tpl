<div class="TD-cat">Login</div>

<!-- BEGIN BLOCK_login_err disabled -->
<div class="TD-content">
	<div class=" List-item error-form">{error_msg}</div>
</div>
<!-- END BLOCK_login_err -->

<form method="post" action="/login/">
	<table class="Main">
	<tr>
		<td align="right">Login:</td>
		<td><input type="text" name="data[login]" value="{login}"></td>
	</tr>
	<tr>
		<td align="right">Parole:</td>
		<td><input type="password" name="data[password]" value="{password}"></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="submit" value=" Login ">
		</td>
	</tr>
	</table>
</form>
