<?php declare(strict_types = 1);

class RegisterTemplate extends AbstractTemplate
{
	public bool $show_register_form = false;
	public bool $show_register_ok = false;

	public int $exp1;
	public int $exp2;

	public ?string $error_l_email = null;
	public ?string $error_l_nick = null;
	public ?string $error_l_password = null;

	public ?string $l_email  = null;
	public ?string $l_nick  = null;
	public ?string $l_password  = null;

	private function register_form(): void
	{ ?>
		<form method="post" action="" onsubmit="this.exp_val.value = parseInt(this.exp1.value) + parseInt(this.exp2.value);">
		<input type="hidden" name="exp1" value="<?=$this->exp1 ?>">
		<input type="hidden" name="exp2" value="<?=$this->exp2 ?>">
		<input type="hidden" name="exp_val">
		<table class="Main">
		<tr>
			<td align="right"<?=$this->error_l_email ?>>E-pasts:</td>
			<td><input type="text" name="data[l_email]" value="<?=specialchars($this->l_email) ?>" autocomplete="email"></td>
			<td>jānorāda pareiza e-pasta adrese, uz kuru tiks nosūtīts pārbaudes kods</td>
		</tr>
		<tr>
			<td align="right"<?=$this->error_l_nick ?>>Segvārds:</td>
			<td><input type="text" name="data[l_nick]" value="<?=specialchars($this->l_nick) ?>" autocomplete="nickname"></td>
			<td>vismaz viens simbols</td>
		</tr>
		<tr>
			<td align="right"<?=$this->error_l_password ?>>Parole:</td>
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
			<td colspan="3"><input type="submit" value=" Reģistrēties " class="DisableOnSubmit"></td>
		</tr>
		</table>
		</form><?
	}

	private function register_ok(): void
	{ ?>
		<div class="TD-content">
			<div class="List-item">
				<p>OK - reģistrācija veiksmīga!</p>
				<p>Uz norādīto epasta adresi tika nosūtīts kods, ar kuru jāaktivizē jaunais profils.</p>
				<p>Ja kautkas noiet greizi, ziņo uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
			</div>
		</div><?
	}

	protected function out(): void
	{ ?>
		<div class="TD-cat">Reģistrācija</div><?

		if($this->show_register_form) {
			$this->register_form();
		}

		if($this->show_register_ok) {
			$this->register_ok();
		}
	}
}
