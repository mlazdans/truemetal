<!-- BEGIN BLOCK_register_error disabled --><font class="error-form">{error_msg}</font><!-- END BLOCK_register_error -->
<!-- BEGIN BLOCK_register_form disabled -->
<div class="TD-cat">
	Reģistrācija
</div>

<form method="post" action="">
<table width="100%">
<tr>
	<td align="right"{error_l_login}>Login:</td>
	<td><input type="text" name="data[l_login]" value="{l_login}" /></td>
	<td>Vismaz 5 simboli (a-z0-9_)</td>
</tr>
<tr>
	<td align="right"{error_l_password}>Parole:</td>
	<td><input type="password" name="data[l_password]" /></td>
	<td>Vismaz 5 simboli (a-z0-9_)</td>
</tr>
<tr>
	<td align="right"{error_l_password} nowrap>Parole 2x:</td>
	<td><input type="password" name="data[l_password2]" /></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_email}>Niks:</td>
	<td><input type="text" name="data[l_nick]" value="{l_nick}" /></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_email}>E-pasts:</td>
	<td><input type="text" name="data[l_email]" value="{l_email}" /></td>
	<td>Jānorāda pareiza e-pasta adrese, uz kuru tiks nosūtīts pārbaudes kods.</td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value=" OK " /></td>
	<td width="100%"></td>
</tr>
</table>
</form>
<!-- END BLOCK_register_form -->

<!-- BEGIN BLOCK_register_ok disabled -->
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
	<td class="TD-cat">Reģistrācija</td>
</tr>
<tr>
	<td colspan="2">OK - reģistrācija veiksmīga!<br>Uz norādīto epasta adresi tu saņemsi adresi ar kodu, ko atverot aktivizēsi savu profilu.<br><br>Ja kautkas noiet greizi, droši ziņo uz <b>info_at_truemetal.lv</b>!</td>
</tr>
</table>

<!-- END BLOCK_register_ok -->

<!-- BEGIN BLOCK_register_code_ok disabled -->
Logins apstiptināts! Tagad tu vari ielogoties!
<!-- END BLOCK_register_code_ok -->
