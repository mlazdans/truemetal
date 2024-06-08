<?php declare(strict_types = 1);

class EmailChangeTemplate extends AbstractTemplate
{
	public string $old_email;
	public string $new_email = "";

	protected function out(): void
	{ ?>
		<div class="TD-cat">E-pasta maiņa</div>

		<form method="post">
		<table class="Main">
		<tr>
			<td align="right">Vecais e-pasts:</td>
			<td><?=specialchars($this->old_email) ?></td>
		</tr>
		<tr>
			<td align="right">Jaunais e-pasts:</td>
			<td><input name="data[new_email]" value="<?=$this->new_email ?>"></td>
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
		</form><?
	}
}
