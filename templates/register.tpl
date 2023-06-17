<div class="TD-cat">Reģistrācija</div>

<!-- BEGIN BLOCK_accept_error disabled -->
<div class="TD-content">
	<div class="List-item  error-form">Diemžēl e-pastu neizdevās apstiprināt!</div>
	<div class="List-item">
		Varianti:<ol>
			<li>nokavēts 15 min. apstiprināšanas termiņš</li>
			<li>nepareizs vai izlietots kods</li>
			<li>e-pasts jau ir apstiprināts</li>
		</ol>
		<p>Ja kas, tad raksti uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
	</div>
</div>
<!-- END BLOCK_accept_error -->

<!-- BEGIN BLOCK_register_form disabled -->
<form method="post" action="" onsubmit="this.exp_val.value = parseInt(this.exp1.value) + parseInt(this.exp2.value);">
<input type="hidden" name="exp1" value="{exp1}">
<input type="hidden" name="exp2" value="{exp2}">
<input type="hidden" name="exp_val">
<table class="Main">
<tr>
	<td align="right"{error_l_email}>E-pasts:</td>
	<td><input type="text" name="data[l_email]" value="{l_email}" autocomplete="email"></td>
	<td>jānorāda pareiza e-pasta adrese, uz kuru tiks nosūtīts pārbaudes kods</td>
</tr>
<tr>
	<td align="right"{error_l_nick}>Segvārds:</td>
	<td><input type="text" name="data[l_nick]" value="{l_nick}" autocomplete="nickname"></td>
	<td>vismaz viens simbols</td>
</tr>
<tr>
	<td align="right"{error_l_password}>Parole:</td>
	<td><input type="password" name="data[l_password]" value="{l_password}" autocomplete="new-password"></td>
	<td></td>
</tr>
<tr>
	<td align="right"{error_l_password} style="white-space: nowrap;">Parole 2x:</td>
	<td><input type="password" name="data[l_password2]" value="{l_password2}"></td>
	<td></td>
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
	<td colspan="3"><input type="submit" value=" Reģistrēties " class="DisableOnSubmit"></td>
</tr>
</table>
</form>
<!-- END BLOCK_register_form -->

<!-- BEGIN BLOCK_register_ok disabled -->
<div class="TD-content">
	<div class="List-item">
		<p>OK - reģistrācija veiksmīga!</p>
		<p>Uz norādīto epasta adresi tika nosūtīts kods, ar kuru jāaktivizē jaunais profils.</p>
		<p>Ja kautkas noiet greizi, ziņo uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
	</div>
</div>
<!-- END BLOCK_register_ok -->

<!-- BEGIN BLOCK_accept_ok disabled -->
<div class="TD-content">
	<div class="List-item">E-pasts apstiptināts veiksmīgi!</div>
</div>
<!-- END BLOCK_accept_ok -->
