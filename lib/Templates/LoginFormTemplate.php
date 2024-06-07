<?php declare(strict_types = 1);

class LoginFormTemplate extends RightItemAbstractTemplate
{
	public ?string $referer = null;
	public ?string $login_nick = null;
	public bool $is_logged = false;

	private function form(): void
	{ ?>
		<div class="List-item">
		<form action="/login/" method="post">
		<table cellpadding="1" cellspacing="1" width="100%">
		<tr>
			<td colspan="2">
				<? if($this->referer) { ?>
					<input type="hidden" name="data[referer]" value="<?=specialchars($this->referer) ?>">
				<? } ?>
				<input style="width: 100%;" type="text" name="data[login]" placeholder="Logins / e-pasts">
			</td>
		</tr>
		<tr>
			<td><input style="width: 100%;" type="password" name="data[password]" size="13" placeholder="Parole"></td>
			<td style="text-align: center;"><input type="submit" class="input" value=" OK "></td>
		</tr>
		</table>
		</form>
		</div>

		<div class="List-item">
			<a href="/register/">Reģistrācija</a>
		</div>
		<div class="List-item">
			<a href="/forgot/">Aizmirsu paroli</a>
		</div><?
	}

	private function info(): void
	{ ?>
		<div class="List-item">
			<?=$this->login_nick ?>
		</div>
		<div class="List-item">
			<a href="/login/logoff/" onclick="return confirm('Tu ko?! Nezini, kas ir Amorphis???');">Log Off</a>
		</div>
		<div class="List-item">
			<a href="/user/profile/" title="Lietotāja profils">Tavs profils</a>
		</div><?
	}

	protected function out(): void
	{
		if($this->is_logged) {
			$this->info();
		} else {
			$this->form();
		}
	}
}
