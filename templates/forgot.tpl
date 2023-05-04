<div class="TD-cat">
	Aizmirsu paroli
</div>

<!-- BEGIN BLOCK_forgot_error disabled -->
<div class="List-item error-form">{error_msg}</div>
<!-- END BLOCK_forgot_error -->

<!-- BEGIN BLOCK_forgot_code_error disabled -->
<div class="error-form">
	<p>Diemžēl šāds pieprasījums netika atrasts!</p>
</div>
<div class="List-item">
	Varianti:<ol>
		<li>nokavēts 3 x 24h apstiprināšanas termiņš</li>
		<li>nepareizs kods</li>
	</ol>
	<p>Ja kas, raksti uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
</div>
<!-- END BLOCK_forgot_code_error -->


<!-- BEGIN BLOCK_forgot_form disabled -->
<form method="post" action="{module_root}/request/">
<table class="Main">
<tr>
	<td colspan="2">Ievadi <b>vienu</b>: savu login <u>vai</u> e-pastu!</td>
</tr>
<tr>
	<td align="right"{error_l_login}>Login:</td>
	<td><input type="text" name="data[l_login]" value="{l_login}" /></td>
</tr>
<tr>
	<td align="right"{error_l_email}>E-pasts:</td>
	<td><input type="text" name="data[l_email]" value="{l_email}" /></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" value=" Pieprasīt jaunu paroli " /></td>
</tr>
</table>
</form>
<!-- END BLOCK_forgot_form -->

<!-- BEGIN BLOCK_forgot_ok disabled -->
<div class="List-item">
	Lai nomainītu paroli, iečeko savu e-pastu ({l_email}), tur Tu
	atradīsi kodu, kuru izmantojot varēsi ievadīt jaunu paroli.
</div>
<div class="List-item">
	Ja kautkas noiet greizi, droši ziņo uz
	<a href="mailto:info@truemetal.lv">info@truemetal.lv</a>
</div>
<!-- END BLOCK_forgot_ok -->

<!-- BEGIN BLOCK_forgot_passw disabled -->
<form method="post" action="{module_root}/accept/{f_code}/">
<table class="Main">
<tr>
	<td colspan="3">
		<input type="hidden" name="change_passw" value="1" />
		Ievadi jauno paroli
	</td>
</tr>
<tr>
	<td align="right">Login:</td>
	<td colspan="2">{l_login}</td>
</tr>
<tr>
	<td align="right">E-pasts:</td>
	<td colspan="2">{l_email}</td>
</tr>
<tr>
	<td align="right">Segvārds:</td>
	<td colspan="2">{l_nick}</td>
</tr>
<tr>
	<td align="right"{error_l_password}>Parole:</td>
	<td><input type="password" name="data[l_password]" /></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_password}>Parole 2x:</td>
	<td colspan="2"><input type="password" name="data[l_password2]" /></td>
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
		<input type="submit" value=" Nomainīt paroli " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_forgot_passw -->

<!-- BEGIN BLOCK_forgot_code_ok disabled -->
<div class="List-item">
	Parole nomainīta! Tagad tu vari mēģināt ielogoties!
</div>
<!-- END BLOCK_forgot_code_ok -->
