<?php declare(strict_types = 1);

class ForgotTemplate extends AbstractTemplate
{
	public ?string $l_email = null;
	public ?string $l_login = null;
	public ?string $l_nick = null;
	public ?string $l_password = null;
	public ?string $l_password2 = null;
	public bool $forgot_form_enabled = false;
	public bool $forgot_pwch_form_enabled = false;
	public bool $is_error = false;
	public bool $is_ok = false;

	private function forgot_form(): void
	{ ?>
		<form method="post">
		<table class="Main">
		<tr>
			<td colspan="2">Ievadi <b>vienu</b>: savu login <u>vai</u> e-pastu!</td>
		</tr>
		<tr>
			<td align="right">E-pasts:</td>
			<td><input type="text" name="data[l_email]" value="<?=specialchars($this->l_email) ?>" autocomplete="email"></td>
		</tr>
		<tr>
			<td align="right">Login:</td>
			<td><input type="text" name="data[l_login]" value="<?=specialchars($this->l_login) ?>" ></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value=" Pieprasīt jaunu paroli " class="DisableOnSubmit"></td>
		</tr>
		</table>
		</form><?
	}

	private function forgot_pwch_form(): void
	{ ?>
		<form method="post">
		<table class="Main">
		<tr>
			<td colspan="3">
				Ievadi jauno paroli
			</td>
		</tr>
		<tr>
			<td align="right">E-pasts:</td>
			<td colspan="2"><?=specialchars($this->l_email) ?></td>
		</tr>
		<tr>
			<td align="right">Segvārds:</td>
			<td colspan="2"><?=specialchars($this->l_nick) ?></td>
		</tr>
		<tr>
			<td align="right">Parole:</td>
			<td><input type="password" name="data[l_password]" value="<?=specialchars($this->l_password) ?>" autocomplete="new-password"></td>
			<td></td>
		</tr>
		<tr>
			<td align="right">Parole 2x:</td>
			<td colspan="2"><input type="password" name="data[l_password2]" value="<?=specialchars($this->l_password2) ?>" autocomplete="new-password"></td>
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
				<input type="submit" value=" Nomainīt paroli ">
			</td>
		</tr>
		</table>
		</form><?
	}

	private function error_block(): void
	{ ?>
		<div class="TD-content">
			<div class="List-item error-form">Diemžēl šāds pieprasījums netika atrasts!</div>
			<div class="List-item">
				Varianti:<ol>
					<li>nokavēts 15 min. apstiprināšanas termiņš</li>
					<li>nepareizs vai izlietots kods</li>
				</ol>
				<p>Ja kas, tad raksti uz <a href="mailto:info@truemetal.lv">info@truemetal.lv</a></p>
			</div>
		</div><?
	}

	private function ok_block(): void
	{ ?>
		<div class="TD-content">
			<div class="List-item">
				Lai nomainītu paroli, iečeko savu e-pastu (<?=$this->l_email ?>), tur Tu
				atradīsi kodu, kuru izmantojot varēsi ievadīt jaunu paroli.
			</div>
			<div class="List-item">
				Ja kautkas noiet greizi, droši ziņo uz
				<a href="mailto:info@truemetal.lv">info@truemetal.lv</a>
			</div>
		</div><?
	}

	protected function out(): void
	{ ?>
		<div class="TD-cat">Aizmirsu paroli</div><?

		if($this->forgot_form_enabled) {
			$this->forgot_form();
		}

		if($this->is_error) {
			$this->error_block();
		}

		if($this->is_ok) {
			$this->ok_block();
		}

		if($this->forgot_pwch_form_enabled) {
			$this->forgot_pwch_form();
		}
	}
}
