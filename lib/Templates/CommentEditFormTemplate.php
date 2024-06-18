<?php declare(strict_types = 1);

class CommentEditFormTemplate extends AbstractTemplate
{
	public string $l_nick;
	public ?string $res_data = null;
	public ?string $res_route = null;
	public ?string $error_msg = null;

	protected function out(): void
	{ ?>
		<div class="TD-cat">Rediģēt komentāru</div>

		<form method="post">
		<input type="hidden" name="action" value="update_comment">
		<table width="100%" cellpadding="2" cellspacing="0">
		<? if($this->error_msg) { ?>
			<tr>
				<td colspan="2" class="error"><?=$this->error_msg ?></td>
			</tr>
		<? } ?>
		<tr>
			<td align="right">Autors:</td>
			<td style="width: 100%"><?=specialchars($this->l_nick) ?></td>
		</tr>
		<tr>
			<td valign="top">Ziņa:</td>
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
