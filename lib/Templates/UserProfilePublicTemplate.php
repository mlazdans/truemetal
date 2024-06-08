<?php declare(strict_types = 1);

class UserProfilePublicTemplate extends AbstractTemplate
{
	public string $l_hash;
	public string $l_email;
	public string $l_nick;
	public ?string $thumb_path = null;
	public string $l_lastaccess;
	public string $l_entered;
	public int $comment_count;
	public int $user_pic_tw;
	public bool $is_blocked;
	public bool $is_comments_disabled = false;
	public bool $show_disable_comments_form = true;
	public bool $is_public_email;

	protected function out(): void
	{
		$l_nick = specialchars($this->l_nick);

		$formatter = new IntlDateFormatter("lv", IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
		$days = floor((time() - strtotime($this->l_lastaccess)) / (3600 * 24));

		if(!$days){
			$l_lastaccess_f = "šodien";
		} elseif($days == 1){
			$l_lastaccess_f = "vakar";
		} elseif($days == 2){
			$l_lastaccess_f = "aizvakar";
		} elseif($days == 3){
			$l_lastaccess_f = "pirms trim dienām";
		} elseif($days < 365){
			$l_lastaccess_f = "pirms $days ".($days % 10 == 1 ? "dienas" : "dienām");
		} else {
			$l_lastaccess_f = $formatter->format(strtotime($this->l_lastaccess));
		}

		$l_entered_f = $formatter->format(strtotime($this->l_entered));

		?>
		<div class="TD-cat">Profils: <?=$l_nick ?><?=($this->is_blocked ? " (bloķēts)" : "") ?></div>
		<div class="TD-content">
		<table>
			<tr>
				<td rowspan="4" class="List-item text-nowrap" style="vertical-align: middle;">
					<? if($this->thumb_path) { ?>
						<div
							class="loading1"
							style="min-height: 50px; min-width: <?=$this->user_pic_tw ?>px;"
						><a
							href="/user/viewimage/<?=$this->l_hash ?>/"
							class="ProfileImage"
							data-hash="<?=$this->l_hash ?>"
							data-nick="<?=$l_nick ?>"
						><img src="/user/thumb/<?=$this->l_hash ?>/" onload="$(this).parent().parent().removeClass('loading1');" alt=""></a></div>
					<? } else { ?>
						<div style="text-align: center; width: <?=$this->user_pic_tw ?>px;">Bildes nav!</div>
					<? } ?>
				</td>
				<th class="List-item">Manīts:</th>
				<td class="List-item w-100"><?=$l_lastaccess_f ?></td>
			</tr>
			<tr>
				<th class="List-item">Pievienojies:</th>
				<td class="List-item"><?=$l_entered_f ?></td>
			</tr>
			<tr>
				<th class="List-item">Komentāri:</th>
				<td class="List-item"><a href="/user/comments/<?=$this->l_hash ?>/"><?=$this->comment_count ?></a></td>
			</tr>
			<tr>
				<th class="List-item">E-pasts:</th>
				<td class="List-item">
					<? if($this->is_public_email) { ?>
						<a href="mailto:<?=$this->l_email ?>"><?=$this->l_email ?></a>
					<? } else { ?>
						<div class="disabled">-nepublicēts e-pasts-</div><?
					} ?>
				</td>
			</tr>
		</table>

		<? if($this->show_disable_comments_form) { ?>
			<form method="post" action="/user/profile/<?=$this->l_hash ?>/">
			<input type="hidden" name="action" value="disable_comments">
			<div class="List-item">
				<label><input
					type="checkbox"
					name="disable_comments"
					onclick="this.form.submit()"
					<?=checkedif($this->is_comments_disabled) ?>
				>Nerādīt šī lietotāja komentārus</label>
			</div>
			</form>
		<? } ?>
		</div><?
	}
}
