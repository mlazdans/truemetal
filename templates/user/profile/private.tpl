<div class="TD-cat">
	Profils: {l_nick}
</div>

<form method="post" enctype="multipart/form-data">
<table class="Main">
<tr>
	<td align="right"><b>Segvārds:</b></td>
	<td>{l_nick}</td>
</tr>
<tr>
	<td align="right"><b>E-pasts:</b></td>
	<td>
		{l_email}
		<label><input type="checkbox" name="data[l_emailvisible]"{l_emailvisible}>redzams citiem</label>
	</td>
</tr>
<tr>
	<td align="right"><b>Parole:</b></td>
	<td class="{bad_pass_class}" style="{bad_pass_style}">{bad_pass_msg}</td>
</tr>
</table>

<div class="List-sep"></div>

<div class="TD-cat">Forums</div>
<table class="Main">
<tr>
	<td style="text-align: right;" {error_l_forumsort_themes}><b>Tēmas kārtot pēc:</b></td>
	<td>
		<label><input type="radio" name="data[l_forumsort_themes]" value="T"{l_forumsort_themes_T}> tēmu datumiem</label>
	</td>
	<td>
		<label><input type="radio" name="data[l_forumsort_themes]" value="C"{l_forumsort_themes_C}> jaunākā komentāra</label>
	</td>
</tr>
<tr>
	<td style="text-align: right;" {error_l_forumsort_msg}><b>Ziņojumus kārtot:</b></td>
	<td>
		<label><input type="radio" name="data[l_forumsort_msg]" value="A"{l_forumsort_msg_A}> pēc datuma augoši</label>
	</td>
	<td>
		<label><input type="radio" name="data[l_forumsort_msg]" value="D"{l_forumsort_msg_D}> pēc datuma dilstoši</label>
	</td>
</tr>
<tr>
	<td colspan="3">
		<label><input type="checkbox" name="data[l_disable_youtube]"{l_disable_youtube_checked}> nerādīt Youtube klipus</label>
	</td>
</tr>
</table>

<div class="List-sep"></div>

<div class="TD-cat">Bilde</div>
<table class="Main">
<!-- BEGIN BLOCK_nopicture disabled -->
<tr>
	<td>Bildes nav!</td>
	<td><input type="file" name="l_picfile"></td>
</tr>
<!-- END BLOCK_nopicture -->
<!-- BEGIN BLOCK_picture disabled -->
<tr>
	<td style="text-align: center;">
		<a
			href="/user/viewimage/{l_hash}/"
			class="ProfileImage"
			data-hash="{l_hash}"
			data-nick="{l_nick}"
		><img src="{thumb_path}" alt=""></a>
	</td>
	<td><input type="file" name="l_picfile"></td>
</tr>
<!-- END BLOCK_picture -->
<tr>
	<td><input type="submit" value=" Saglabāt "></td>
	<td>
		<a class="button" href="/user/pwch/">Mainīt paroli</a>
		<a class="button" href="/user/emailch/">Mainīt e-pastu</a>
		<!-- BEGIN BLOCK_picture_del disabled -->
		<a class="button" href="/user/profile/?action=deleteimage" onclick="return confirm('Pārliecināts?');">Dzēst bildi</a>
		<!-- END BLOCK_picture_del -->
	</td>
</tr>
</table>
</form>

<div class="List-sep"></div>

<!-- BEGIN BLOCK_truecomments disabled -->
<div class="TD-cat">{truecomment_msg}</div>

<table class="Main">
<!-- BEGIN BLOCK_truecomment_item -->
<tr>
	<td><a href="{res_href}">{res_data}</a></td>
	<td class="vote-plus">{res_votes_plus_count}</td>
	<td class="vote-minus">{res_votes_minus_count}</td>
</tr>
<!-- END BLOCK_truecomment_item -->
</table>

<div class="List-sep"></div>
<!-- END BLOCK_truecomments -->
