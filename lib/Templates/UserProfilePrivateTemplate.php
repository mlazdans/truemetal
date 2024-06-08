<?php declare(strict_types = 1);

class UserProfilePrivateTemplate extends AbstractTemplate
{
	public ?ViewResCollection $TopRatedRes = null;
	public ?ViewResCollection $LessRatedRes = null;
	public ?string $thumb_path = null;
	public string $l_nick;
	public string $l_hash;
	public string $l_email;
	public int $passw_status;
	public bool $is_emailvisible;
	public bool $is_themes_sorted_by_newest_comment;
	public bool $is_comments_sorted_by_latest_date;
	public bool $is_youtube_disabled;

	protected function out(): void
	{
		$bad_passw_class = 'blink';
		$bad_passw_style = 'color: red';

		if(($this->passw_status & Logins::PASSW_STATUS_BRUTE) && ($this->passw_status & Logins::PASSW_STATUS_DICT)){
			$bad_passw_msg = "Apsveicam! Tava parole ir gan paroļu vārdnīcā gan viegli atlaužama! Nomaini!";
		} elseif($this->passw_status & Logins::PASSW_STATUS_DICT){
			$bad_passw_msg = "Tava parole ir paroļu vārdnīcā! Nomaini!";
		} elseif($this->passw_status & Logins::PASSW_STATUS_BRUTE){
			$bad_passw_msg = "Tava parole ir viegli atlaužama! Nomaini!";
		} else {
			$bad_passw_msg = "Apsveicam! Tava parole nav paroļu vārdnīcā vai viegli atlaužama!";
			$bad_passw_style = 'color: #00a400';
			$bad_passw_class = '';
		}

		?>
		<div class="TD-cat">
			Profils: <?=$this->l_nick ?>
		</div>

		<form method="post" enctype="multipart/form-data">
		<table class="Main">
		<tr>
			<td style="text-align: right;"><b>Segvārds:</b></td>
			<td colspan="2"><?=$this->l_nick ?></td>
		</tr>
		<tr>
			<td style="text-align: right;"><b>E-pasts:</b></td>
			<td colspan="2">
				<?=$this->l_email ?>
				<label><input <?=checkedif($this->is_emailvisible) ?> type="checkbox" value="1" name="data[l_emailvisible]">redzams citiem</label>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;"><b>Parole:</b></td>
			<td colspan="2" class="<?=$bad_passw_class ?>" style="<?=$bad_passw_style ?>"><?=$bad_passw_msg ?></td>
		</tr>
		<tr>
			<td style="text-align: right;"><b>Foruma tēmas kārtot pēc:</b></td>
			<td>
				<label><input <?=checkedif(!$this->is_themes_sorted_by_newest_comment) ?> type="radio" name="data[l_forumsort_themes]" value="0"> tēmu datumiem</label>
			</td>
			<td>
				<label><input <?=checkedif($this->is_themes_sorted_by_newest_comment) ?> type="radio" name="data[l_forumsort_themes]" value="1"> jaunākā komentāra</label>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;"><b>Komentārus kārtot:</b></td>
			<td>
				<label><input <?=checkedif($this->is_comments_sorted_by_latest_date) ?> type="radio" name="data[l_forumsort_msg]" value="0"> pēc datuma augoši</label>
			</td>
			<td>
				<label><input <?=checkedif(!$this->is_comments_sorted_by_latest_date) ?> type="radio" name="data[l_forumsort_msg]" value="1"> pēc datuma dilstoši</label>
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<label><input <?=checkedif($this->is_youtube_disabled) ?> type="checkbox" value="1" name="data[l_disable_youtube]"> nerādīt Youtube video</label>
			</td>
		</tr>
		</table>

		<div class="List-sep"></div>

		<div class="TD-cat">Bilde</div>
		<table class="Main">
		<? if($this->thumb_path) { ?>
			<tr>
				<td style="text-align: center;">
					<a
						href="/user/viewimage/<?=$this->l_hash ?>/"
						class="ProfileImage"
						data-hash="<?=$this->l_hash ?>"
						data-nick="<?=$this->l_nick ?>"
					><img src="<?=$this->thumb_path ?>" alt=""></a>
				</td>
				<td><input type="file" name="l_picfile"></td>
			</tr>
		<? } else { ?>
			<tr>
				<td>Bildes nav!</td>
				<td><input type="file" name="l_picfile"></td>
			</tr>
		<? } ?>
		</table>

		<div class="List-sep"></div>
		<div style="display: flex;">
			<div><input type="submit" value=" Saglabāt "></div>
			<div style="justify-content:flex-end; margin-left: auto;">
				<a class="button" href="/user/pwch/">Mainīt paroli</a>
				<a class="button" href="/user/emailch/">Mainīt e-pastu</a>
				<a class="button" href="/mark/" title="Atzīmēt visus komentārus un tēmas kā lasītus">Atzīmēt kā lasītu</a>
				<? if($this->thumb_path) { ?>
					<a class="button" href="/user/profile/?action=deleteimage" onclick="return confirm('Pārliecināts?');">Dzēst bildi</a>
				<? } ?>
			</div>
		</div>

		</form>

		<div class="List-sep"></div><?

		if($this->TopRatedRes->count()){
			$this->res('Visvairāk plusotie ieraksti:', $this->TopRatedRes);
		}

		if($this->LessRatedRes->count()){
			$this->res('Visvairāk mīnusotie ieraksti:', $this->LessRatedRes);
		}
	}

	private function res(string $title, ViewResCollection $res): void
	{ ?>
		<div class="TD-cat"><?=$title ?></div>

		<table class="Main">
		<? foreach($res as $item) {
			$res_data = $item->res_data;
			if(mb_strlen($res_data) > 70){
				$res_data = mb_substr($res_data, 0, 70).'...';
			}

			?>
			<tr>
				<td><a href="<?=$item->res_route ?>"><?=specialchars($res_data) ?></a></td>
				<td class="vote-plus"><?=$item->res_votes_plus_count ?></td>
				<td class="vote-minus"><?=$item->res_votes_minus_count ?></td>
			</tr>
		<? } ?>
		</table>

		<div class="List-sep"></div><?
	}
}
