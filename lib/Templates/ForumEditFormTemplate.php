<?php declare(strict_types = 1);

class ForumEditFormTemplate extends AbstractTemplate
{
	public string $res_nickname;
	public string $res_data;
	public string $res_name;
	public string $res_route;
	public ?string $error_msg = null;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Rediģēt forumu:</div>

		<form method="post">
		<input type="hidden" name="action" value="update_forum">
		<table width="100%" cellpadding="2" cellspacing="0">
		<? if($this->error_msg) { ?>
			<tr>
				<td colspan="2" class="error"><?=$this->error_msg ?></td>
			</tr>
		<? } ?>
		<tr>
			<td align="right">Autors:</td>
			<td style="width: 100%"><?=specialchars($this->res_nickname) ?></td>
		</tr>
		<tr>
			<td valign="top" align="right">Nosaukums:</td>
			<td style="width: 100%"><input type="text" name="res_name" value="<?=specialchars($this->res_name) ?>" size="64" style="width: 100%"></td>
		</tr>
		<tr>
			<td valign="top" align="right">Ziņa:</td>
			<td>
				<textarea name="res_data" cols="50" rows="15" style="width: 100%;"><?=specialchars($this->res_data) ?></textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value=" Saglabāt " class="DisableOnSubmit">
				<a href="<?=$this->res_route ?>" class="button"> Atcelt </a>
			</td>
		</tr>
		</table>
		</form><?
	}
}
