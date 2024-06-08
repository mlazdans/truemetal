<?php declare(strict_types = 1);

class PwchTemplate extends AbstractTemplate
{
	// public bool $hide_passw_manager = false;
	public string $old_password;
	public string $l_password;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Paroles maiņa</div>

		<form method="post">
		<table class="Main">
		<tr>
			<td align="right">Vecā parole:</td>
			<td><input type="password" name="data[old_password]" value="<?=specialchars($this->old_password) ?>" autocomplete="password"></td>
			<td></td>
		</tr>
		<tr>
			<td align="right">Jaunā parole:</td>
			<td><input type="password" name="data[l_password]" value="<?=specialchars($this->l_password) ?>" autocomplete="new-password"></td>
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
			<td colspan="3">
				<input type="submit" value=" Mainīt paroli ">
			</td>
		</tr>
		</table>
		</form><?
	}
}
