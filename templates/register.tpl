<div class="TD-cat">
	Reģistrācija
</div>

<!-- BEGIN BLOCK_register_error disabled -->
<div class=" List-item error-form">{error_msg}</div>
<!-- END BLOCK_register_error -->

<!-- BEGIN BLOCK_accept_error disabled -->
<div class="error-form">
	<p>Diemžēl loginu neizdevās apstiprināt!</p>
</div>
<div class="List-item">
	Varianti:<ol>
		<li>nokavēts 3 x 24h apstiprināšanas termiņš</li>
		<li>nepareizs kods</li>
		<li>logins jau ir apstiprināts</li>
	</ol>
	<p>Ja kas, raksti uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
</div>
<!-- END BLOCK_accept_error -->

<!-- BEGIN BLOCK_register_form disabled -->
<form method="post" action="">
<table class="Main">
<tr>
	<td align="right"{error_l_login}>Login:</td>
	<td><input type="text" name="data[l_login]" value="{l_login}" /></td>
	<td>vismaz 5 simboli (a-z0-9_)</td>
</tr>
<tr>
	<td align="right"{error_l_password}>Parole:</td>
	<td><input type="password" name="data[l_password]" value="{l_password}" /></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_password} style="white-space: nowrap;">Parole 2x:</td>
	<td><input type="password" name="data[l_password2]" value="{l_password2}" /></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_nick}>Segvārds:</td>
	<td><input type="text" name="data[l_nick]" value="{l_nick}" /></td>
	<td>vismaz viens simbols</td>
</tr>
<tr>
	<td align="right"{error_l_email}>E-pasts:</td>
	<td><input type="text" name="data[l_email]" value="{l_email}" /></td>
	<td>Jānorāda pareiza e-pasta adrese, uz kuru tiks nosūtīts pārbaudes kods.</td>
</tr>
<tr>
	<td></td>
	<td colspan="2">
		<div>Parolei jāatbilst visiem zemāk minētajiem kritējiem:</div>
		<ul style="margin-top: 0;">
			<li>vismaz 10 simbolu gara</li>
			<li>jāsatur burts (bez garumzīmes)</li>
			<li>jāsatur ne-burts (cipars, burts ar garumzīmi, pietruzīme, utml.)</li>
			<li>Nav secīgu simbolu, piemēram, &quot;aaa&quot;</li>
		</ul>
	</td>
</tr>
<tr>
	<td colspan="3"><input type="submit" value=" Reģistrēties " /></td>
</tr>
</table>
</form>
<!-- END BLOCK_register_form -->

<!-- BEGIN BLOCK_register_ok disabled -->
<div class="List-item">
	<p>OK - reģistrācija veiksmīga!</p>
	<p>Uz norādīto epasta adresi tika nosūtīts kods, ar kuru jāaktivizē jaunais profils.</p>
	<p>Ja kautkas noiet greizi, ziņo uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
</div>
<!-- END BLOCK_register_ok -->

<!-- BEGIN BLOCK_accept_ok disabled -->
<div class="List-item">
	Logins apstiptināts! Tagad Tu vari ielogoties!
</div>
<!-- END BLOCK_accept_ok -->

