<!-- BEGIN BLOCK_not_loged disabled -->
<div class="Info">
	TrueMetal!
</div>
<!-- END BLOCK_not_loged -->

<!-- BEGIN BLOCK_profile disabled -->
<div class="TD-cat">
	Profils: {l_nick}
</div>
<!-- BEGIN BLOCK_profile_error disabled -->
<div class="List-item Info error">
	{error_msg}
</div>
<!-- END BLOCK_profile_error -->

<form action="" method="post" enctype="multipart/form-data" id="profile_edit">
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
	<td colspan="2">Bildes nav!</td>
</tr>
<!-- END BLOCK_nopicture -->
<tr>
	<td align="right"><b>Mainīt:</b></td>
	<td><input type="file" name="l_picfile"></td>
</tr>
<!-- BEGIN BLOCK_picture disabled -->
<tr>
	<td colspan="2">
		<a href="/user/viewimage/{l_login}/" onclick="Truemetal.viewProfileImage('{l_login}', {pic_w}, {pic_h}, '{l_login}'); return false;"><img src="{pic_path}" alt=""></a>
	</td>
</tr>
<tr>
	<td colspan="2"><a href="/user/profile/?action=deleteimage" onclick="return confirm('Pārliecināts?');">Dzēst</a></td>
</tr>
<!-- END BLOCK_picture -->
<tr>
	<td colspan="2">
		<input type="submit" value=" Saglabāt ">
		<a href="{module_root}/pwch/">Mainīt paroli</a>
		<a href="{module_root}/emailch/">Mainīt e-pastu</a>
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
	<td><a href="{c_href}">{c_data}</a></td>
	<td class="vote-plus">{plus_count}</td>
	<td class="vote-minus">{minus_count}</td>
</tr>
<!-- END BLOCK_truecomment_item -->
</table>

<div class="List-sep"></div>
<!-- END BLOCK_truecomments -->

<!-- END BLOCK_profile -->
