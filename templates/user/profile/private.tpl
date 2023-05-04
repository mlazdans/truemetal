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
<div class="List-item error">
	{error_msg}
</div>
<!-- END BLOCK_profile_error -->

<form action="" method="post" enctype="multipart/form-data" id="profile_edit">
<table class="Main">
<tr>
	<td align="right"{error_l_email}><b>Segvārds:</b></td>
	<td>{l_nick}</td>
</tr>
<tr>
	<td align="right"{error_l_email}><b>E-pasts:</b></td>
	<td>
		<input type="text" name="data[l_email]" value="{l_email}" />
		<label><input type="checkbox" name="data[l_emailvisible]"{l_emailvisible} />redzams citiem</label>
	</td>
</tr>
<tr>
	<td align="right"><b>Bilde:</b></td>
	<td><input type="file" name="l_picfile" /></td>
</tr>
<tr>
	<td align="right"{error_l_password}><b>Parole:</b></td>
	<td><input type="password" name="data[l_password]" value="{l_password}" /></td>
</tr>
<tr>
	<td align="right"{error_l_password} style="white-space: nowrap;"><b>Parole 2x:</b></td>
	<td><input type="password" name="data[l_password2]" value="{l_password2}" /></td>
</tr>
<tr>
	<td colspan="2"><input type="submit" value=" Saglabāt " /></td>
</tr>
</table>

<div class="List-sep"></div>

<div class="TD-cat">
	Bilde:
</div>

<table class="Main">
<!-- BEGIN BLOCK_nopicture disabled -->
<tr>
	<td>Bildes nav!</td>
</tr>
<!-- END BLOCK_nopicture -->
<!-- BEGIN BLOCK_picture disabled -->
<tr>
	<td>
		<a href="/user/viewimage/{l_login}/" onclick="Truemetal.viewProfileImage('{l_login}', {pic_w}, {pic_h}, '{l_login}'); return false;"><img src="{pic_path}" alt="" /></a>
	</td>
</tr>
<!-- BEGIN BLOCK_picture_delete disabled -->
<tr>
	<td style="text-align: center"><a href="{module_root}/deleteimage/" onclick="return confirm('Pārliecināts?');">Dzēst</a></td>
</tr>
<!-- END BLOCK_picture_delete -->
<!-- END BLOCK_picture -->
</table>

<div class="List-sep"></div>

<div class="TD-cat">
	Forums
</div>
<table class="Main">
<tr>
	<td style="text-align: right;" {error_l_forumsort_themes}><b>Tēmas kārtot pēc:</b></td>
	<td>
		<label><input type="radio" name="data[l_forumsort_themes]" value="T"{l_forumsort_themes_T} /> tēmu datumiem</label>
	</td>
	<td>
		<label><input type="radio" name="data[l_forumsort_themes]" value="C"{l_forumsort_themes_C} /> jaunākā komentāra</label>
	</td>
</tr>
<tr>
	<td style="text-align: right;" {error_l_forumsort_msg}><b>Ziņojumus kārtot:</b></td>
	<td>
		<label><input type="radio" name="data[l_forumsort_msg]" value="A"{l_forumsort_msg_A} /> pēc datuma augoši</label>
	</td>
	<td>
		<label><input type="radio" name="data[l_forumsort_msg]" value="D"{l_forumsort_msg_D} /> pēc datuma dilstoši</label>
	</td>
</tr>
<tr>
	<td colspan="3">
		<label><input type="checkbox" name="data[l_disable_youtube]"{l_disable_youtube_checked} /> nerādīt Youtube klipus</label>
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

<div class="TD-cat">
	Palīdzība
</div>
<ul>
	<li>
		<div>Lai nomainītu paroli, tā jāievada abos lauciņos</div>
		<div>Parolei jāatbilst <u>visiem</u> zemāk minētajiem kritējiem:</div>
		<ul style="margin-top: 0;">
			<li>vismaz 10 simbolu gara</li>
			<li>jāsatur burts (bez garumzīmes)</li>
			<li>jāsatur ne-burts (cipars, burts ar garumzīmi, pietruzīme, utml.)</li>
			<li>nav secīgu simbolu, piemēram, &quot;aaa&quot;</li>
		</ul>
		<div>Paroles tiek glabātas šifrētas ar <a href="https://en.wikipedia.org/wiki/Cryptographic_hash_function">vienvirziena algoritmu</a></div>
	</li>

	<li>Ja tiek mainīts e-pasts, konts kļūs neaktīvs, kamēr tas netiks apstiprināts tāpat kā reģistrējoties (kods tiks saņemts uz jauno e-pastu)</li>
	<li>Bildes izmēri nedrīkst pārsniegt {user_pic_w} x {user_pic_h}. Ja kāda no dimensijām pārsniegs dotos izmērus, tā tiks automātiski samazināta. Mazā bilde tiek ģenerēta automātiski (ne lielāka par {user_pic_tw} x {user_pic_th}). Pieņemti tiek faili 'image/gif', 'image/jpeg', 'image/pjpeg'</li>
	<li>Aizliegts XXX, citu cilvēku bildes u.c. drazu!</li>
</ul>
<!-- END BLOCK_profile -->

