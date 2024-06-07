<?php declare(strict_types = 1);

class ThemeEditFormTemplate extends AbstractTemplate
{
	public string $nick_name;
	public ?string $name = null;
	public ?string $data = null;
	public ?string $error_msg = null;
	public bool $ignore_forum_name_strlen = false;

	protected function out(): void
	{ ?>
		<a name="add_theme"></a>
		<form action="#add_theme" method="post">
		<? if($this->ignore_forum_name_strlen) { ?>
			<input type="hidden" name="data[ignore_forum_name_strlen]" value="1">
		<? } ?>

		<? if($this->error_msg) { ?>
			<div class="error"><?=$this->error_msg ?></div>
		<? } ?>

		<table class="Forum-Theme-form" cellpadding="2" cellspacing="1">
		<tr>
			<td align="right">
				<input type="hidden" name="action" value="add_theme">
				Segvārds:
			</td>
			<td style="width: 100%;"><?=$this->nick_name ?></td>
		</tr>
		<tr>
			<td style="white-space: nowrap;" align="right">Jauna tēma:</td>
			<td><input style="width: 95%;" type="text" name="data[forum_name]" maxlength="255" size="64" value="<?=$this->name ?>"></td>
		</tr>
		<tr>
			<td align="right" valign="top">Ziņa:</td>
			<td><textarea style="width: 95%;" name="data[forum_data]" cols="50" rows="10"><?=$this->data ?></textarea></td>
		</tr>
		<tr>
			<td align="right">&nbsp;</td>
			<td><input type="submit" value=" Pievienot " class="DisableOnSubmit"></td>
		</tr>
		</table>
		</form><?
	}
}
