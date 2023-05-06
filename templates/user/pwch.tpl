<div class="TD-cat">Paroles maiņa</div>

<!-- BEGIN BLOCK_not_loged disabled -->
<div class="Info">TrueMetal!</div>
<!-- END BLOCK_not_loged -->


<!-- BEGIN BLOCK_pwch_error disabled -->
<div class="List-item Info error-form">{error_msg}</div>
<!-- END BLOCK_pwch_error -->


<!-- BEGIN BLOCK_pwch_form disabled -->
<form method="post">
<table class="Main">
<tr>
	<td align="right"{error_old_password}>Vecā parole:</td>
	<td><input type="password" name="data[old_password]" value="{old_password}"></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_password}>Jaunā parole:</td>
	<td><input type="password" name="data[l_password]" value="{l_password}"></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_password}>Jaunā parole 2x:</td>
	<td colspan="2"><input type="password" name="data[l_password2]" value="{l_password2}"></td>
</tr>
<tr>
	<td></td>
	<td colspan="2">
		<div>Parolei jāatbilst <u>visiem</u> zemāk minētajiem kritējiem:</div>
		<ul style="margin-top: 0;">
			<li>vismaz 10 simbolu gara</li>
			<li>jāsatur burts (bez garumzīmes)</li>
			<li>jāsatur ne-burts (cipars, burts ar garumzīmi, pietruzīme, utml.)</li>
			<li>nav secīgu simbolu, piemēram, &quot;aaa&quot;</li>
		</ul>
	</td>
</tr>
<tr>
	<td colspan="3">
		<input type="submit" value=" Mainīt paroli ">
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_pwch_form -->

<!-- BEGIN BLOCK_pwch_ok disabled -->
<div class="List-item Info">
	Parole nomainīta!
</div>
<!-- END BLOCK_pwch_ok -->
