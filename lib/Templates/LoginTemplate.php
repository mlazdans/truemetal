<?php declare(strict_types = 1);

class LoginTemplate extends AbstractTemplate
{
	public ?string $error_msg = null;
	public ?string $login = null;
	public ?string $password = null;
	public ?string $referer = null;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Login</div>

		<? if($this->error_msg) { ?>
			<div class="TD-content">
				<div class=" List-item error-form"><?=$this->error_msg ?></div>
			</div>
		<? } ?>

		<form method="post" action="/login/">
			<input type="hidden" name="referer" value="<?=specialchars($this->referer) ?>">
			<table class="Main">
			<tr>
				<td align="right">Login:</td>
				<td><input type="text" name="data[login]" value="<?=specialchars($this->login) ?>"></td>
			</tr>
			<tr>
				<td align="right">Parole:</td>
				<td><input type="password" name="data[password]" value="<?=specialchars($this->password) ?>"></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" value=" Login ">
				</td>
			</tr>
			</table>
		</form><?
	}
}
