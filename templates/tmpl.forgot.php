<!-- BEGIN BLOCK_forgot_error disabled --><font class="error-form" style="color:red;">{error_msg}</font><!-- END BLOCK_forgot_error -->
<!-- BEGIN BLOCK_forgot_form disabled -->
<form method="post" action="{module_root}/request/">
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td class="TD-cat" colspan="2">Aizmirsu paroli</td>
</tr>
</table>

<table cellpadding="2" cellspacing="2" width="100%" bgcolor="#330000">
<tr>
	<td colspan="2">Ievadi savu loginu vai e-pastu!</ts>
</tr>
<tr>
	<td align="right"{error_l_login}>Login:</td>
	<td><input type="text" name="data[l_login]" value="{l_login}"></td>
</tr>
<tr>
	<td align="right"{error_l_email}>E-pasts:</td>
	<td><input type="text" name="data[l_email]" value="{l_email}"></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value=" Pieprasīt jaunu paroli "></td>
</tr>
</table>
</form>
<!-- END BLOCK_forgot_form -->

<!-- BEGIN BLOCK_forgot_ok disabled -->
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td class="TD-cat">Aizmirsu paroli</td>
</tr>
<tr>
	<td>Lai no mainītu paroli, iečeko savu e-pastu ({l_email}), tur Tu atradīsi www adresi, kuru aktivizējot varēsi ievadīt jauno paroli.
	<br><br>Ja kautkas noiet greizi, droši ziņo uz <b>info_at_truemetal.lv</b>!</td>
</tr>
</table>
<!-- END BLOCK_forgot_ok -->

<!-- BEGIN BLOCK_forgot_passw disabled -->
<form method="post" action="{module_root}/accept/{f_code}/">
<input type="hidden" name="change_passw" value="1">
<table cellpadding="1" cellspacing="1" width="100%">
<tr>
	<td colspan="3" class="TD-cat">Aizmirsu paroli</td>
</tr>
<tr>
	<td colspan="3">Ievadi jauno paroli</td>
</tr>
<tr>
	<td align="right">Login:</td>
	<td>{l_login}</td>
</tr>
<tr>
	<td align="right">E-pasts:</td>
	<td>{l_email}</td>
</tr>
<tr>
	<td align="right">Niks:</td>
	<td>{l_nick}</td>
</tr>
<tr>
	<td align="right"{error_l_password}>Parole:</td>
	<td><input type="password" name="data[l_password]"></td>
	<td width="100%">Vismaz 5 simboli (a-z0-9_)</td>
</tr>
<tr>
	<td align="right"{error_l_password} nowrap>Parole 2x:</td>
	<td><input type="password" name="data[l_password2]"></td>
	<td></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value=" Nomainīt paroli "></td>
</tr>
</table>
</form>
<!-- END BLOCK_forgot_passw -->

<!-- BEGIN BLOCK_forgot_code_ok disabled -->
Parole nomainīta! Tagad tu vari mēģināt ielogoties!
<!-- END BLOCK_forgot_code_ok -->
