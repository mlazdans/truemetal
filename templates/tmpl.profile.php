<!-- BEGIN BLOCK_not_loged disabled -->
<div class="Info">
	TrueMetal!
</div>
<!-- END BLOCK_not_loged -->

<!-- BEGIN BLOCK_no_suck_login disabled -->
<div class="Info">
	Šāds profils neeksitē!
</div>
<!-- END BLOCK_no_suck_login -->

<!-- BEGIN BLOCK_profile_error disabled -->
<div class="error">
	{error_msg}
</div>
<!-- END BLOCK_profile_error -->

<!-- BEGIN BLOCK_profile disabled -->
<div class="TD-cat">
	Profils: {l_nick}
</div>

<!-- BEGIN BLOCK_public_email disabled -->
<div class="List-item">
	<b>E-pasts:</b> <a href="mailto:{l_email}">{l_email}</a>
</div>
<!-- END BLOCK_public_email -->


<!-- BEGIN BLOCK_private_profile disabled -->
<form action="" method="post" enctype="multipart/form-data" id="profile_edit">
<table cellpadding="2" cellspacing="1">
<tr>
	<td align="right"{error_l_email}><b>Niks:</b></td>
	<td>{l_nick}</td>
</tr>
<tr>
	<td align="right"{error_l_email}><b>E-pasts:</b></td>
	<td>
		<input type="text" name="data[l_email]" value="{l_email}" />
		<input type="checkbox" name="data[l_emailvisible]"{l_emailvisible} /> - redzams citiem
	</td>
</tr>
<tr>
	<td align="right"><b>Bilde:</b></td>
	<td><input type="file" name="l_picfile" /></td>
</tr>
<tr>
	<td align="right"{error_l_password}><b>Parole:</b></td>
	<td><input type="password" name="data[l_password]" /></td>
</tr>
<tr>
	<td align="right"{error_l_password} style="white-space: nowrap;"><b>Parole 2x:</b></td>
	<td><input type="password" name="data[l_password2]" /></td>
</tr>
</table>

<div class="List-sep"></div>

<div class="TD-cat">
	Bilde:
</div>

<table cellpadding="0" cellspacing="1">
<!-- BEGIN BLOCK_nopicture disabled -->
<tr>
	<td>Bildes nav!</td>
</tr>
<!-- END BLOCK_nopicture -->
<!-- BEGIN BLOCK_picture disabled -->
<tr>
	<td><a href="{module_root}/view/{l_id}/" onclick="Truemetal.Pop('{module_root}/view/{l_id}/', {pic_w}, {pic_h}); return false;"><img src="{pic_path}" alt="" border="0"></a></td>
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

<table cellpadding="2" cellspacing="1">
<tr>
	<td colspan="2"{error_l_forumsort_themes}><b>Tēmas kārtot pēc:</b></td>
</tr>
<tr>
	<td colspan="2">
		<input type="radio" name="data[l_forumsort_themes]" value="T"{l_forumsort_themes_T} /> tēmu datumiem
		<input type="radio" name="data[l_forumsort_themes]" value="C"{l_forumsort_themes_C} /> jaunākā komentāra
	</td>
</tr>
<tr>
	<td colspan="2"{error_l_forumsort_msg}><b>Ziņojumus kārtot:</b></td>
</tr>
<tr>
	<td colspan="2">
		<input type="radio" name="data[l_forumsort_msg]" value="A"{l_forumsort_msg_A} /> pēc datuma augoši
		<input type="radio" name="data[l_forumsort_msg]" value="D"{l_forumsort_msg_D} /> pēc datuma dilstoši
	</td>
</tr>
<tr>
	<td colspan="2">
		<input id="l_disable_avatars" type="checkbox" name="data[l_disable_avatars]"{l_disable_avatars_checked} />
		<label for="l_disable_avatars">Nerādīt avatārus</label>
	</td>
</tr>
</table>

<div class="List-sep"></div>

<div class="TD-cat">
	Palīdzība
</div>
<ul>
	<li>Lai nomainītu paroli, tā jāievada abos lauciņos (ja negrib nomainīt - jāatstājs tukšs)</li>
	<li>Parole datubāzē tiek glabāta šifrēta ar vienvirziena algoritmu - tātad nevienam neizlasāma.</li>
	<li>Ja tiks mainīts e-pasts, konts kļūs neaktīvs, kamēr tas netiks apstiprināts tāpat kā reģistrējoties (kods tiks saņemts uz jauno e-pastu)</li>
	<li>Bildes izmēri nedrīksts pārsniegt {user_pic_w} x {user_pic_h}. Ja ja kāda no dimensijām pārsniegs dotos izmērus, tā tiks automātiski samazināta. Mazā bilde tiek ģenerēta automātiski (ne lielāka par {user_pic_tw} x {user_pic_th}). Pieņemti tiek faili 'image/gif', 'image/jpeg', 'image/pjpeg'</li>
	<li>Aizliegts pievienot XXX, LV likumdošanai neatbildstošas, citu cilvēku bildes un tamldz. drazu!</li>
</ul>

<div>
	<input type="submit" value=" Saglabāt " />
</div>
</form>
<!-- END BLOCK_private_profile -->


<!-- END BLOCK_profile -->

