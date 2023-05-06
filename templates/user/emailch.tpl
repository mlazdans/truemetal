<div class="TD-cat">E-pasta maiņa</div>

<!-- BEGIN BLOCK_not_loged disabled -->
<div class="Info">TrueMetal!</div>
<!-- END BLOCK_not_loged -->

<!-- BEGIN BLOCK_maint disabled -->
<div class="Info">Pag bik :)</div>
<!-- END BLOCK_maint -->


<!-- BEGIN BLOCK_emailch_error disabled -->
<div class="List-item Info error-form">{error_msg}</div>
<!-- END BLOCK_emailch_error -->

<!-- BEGIN BLOCK_emailch_msg disabled -->
<div class="List-item Info">{msg}</div>
<!-- END BLOCK_emailch_msg -->

<!-- BEGIN BLOCK_emailch_form disabled -->
<form method="post">
<table class="Main">
<tr>
	<td align="right">Vecais e-pasts:</td>
	<td>{old_email}</td>
</tr>
<tr>
	<td align="right"{error_new_email}>Jaunais e-pasts:</td>
	<td><input name="data[new_email]" value="{new_email}"></td>
</tr>
<tr>
	<td></td>
	<td>
		<div>Uz jauno e-pastu tiks nosūtīts apstiprināšanas kods. Tikmēr aktīvs būs vecais e-pasts.</div>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value=" Mainīt e-pastu ">
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_emailch_form -->
