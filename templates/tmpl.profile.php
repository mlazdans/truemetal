<!-- BEGIN BLOCK_not_loged disabled -->
TrueMetal!
<!-- END BLOCK_not_loged -->

<!-- BEGIN BLOCK_no_suck_login disabled -->
Šāds profils neeksitē!
<!-- END BLOCK_no_suck_login -->

<!-- BEGIN BLOCK_profile_error disabled -->
<span class="error">{error_msg}</span>
<!-- END BLOCK_profile_error -->

<!-- BEGIN BLOCK_profile disabled -->
<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
	<td class="TD-cat" colspan="2">Profils: {l_nick}</td>
</tr>
</table>

<table cellpadding="0" cellspacing="1" border="0" width="100%" bgcolor="#330000">
<tr>
	<td valign="top" width="120">
		<table cellpadding="2" cellspacing="1" border="0" align="center">
		<!-- BEGIN BLOCK_nopicture disabled -->
		<tr>
			<td class="TD-cat">Bilde:</td>
		</tr>
		<tr>
			<td nowrap>Bildes nav!</td>
		</tr>
		<!-- END BLOCK_nopicture -->
		<!-- BEGIN BLOCK_picture disabled -->
		<tr>
			<td class="TD-cat">Bilde:</td>
		</tr>
		<tr>
			<td nowrap><a href="{module_root}/view/{l_id}/" onclick="pop('{module_root}/view/{l_id}/', {pic_w}, {pic_h}); return false;"><img src="{pic_path}" alt="" border="0"></a></td>
		</tr>
		<!-- BEGIN BLOCK_picture_delete disabled -->
		<tr>
			<td style="text-align: center"><a href="{module_root}/deleteimage/" onClick="return checkDelSimple();">Dzēst</a></td>
		</tr>
		<!-- END BLOCK_picture_delete -->
		<!-- END BLOCK_picture -->
		</table>
	</td>
	<td valign="top" width="100%">
		<table cellpadding="2" cellspacing="1" border="0" width="100%">
		<tr>
			<td class="TD-cat" colspan="2">Lietotāja informācija</td>
		</tr>
		<tr>
			<td align="right"{error_l_email}><b>Niks:</b></td>
			<td>{l_nick}</td>
		</tr>
		<!-- BEGIN BLOCK_public_email disabled -->
		<tr>
			<td align="right"{error_l_email}><b>E-pasts:</b></td>
			<td><a href="mailto:{l_email}">{l_email}</a></td>
		</tr>
		<!-- END BLOCK_public_email -->
		<!-- BEGIN BLOCK_private_profile disabled -->
		<form method="post" enctype="multipart/form-data" name="profile_edit">
		<tr>
			<td align="right"{error_l_email}><b>E-pasts:</b></td>
			<td><input type="text" name="data[l_email]" value="{l_email}"> <input type="checkbox" name="data[l_emailvisible]"{l_emailvisible}> - redzams citiem</td>
		</tr>
		<tr>
			<td align="right"><b>Bilde:</b></td>
			<td><input type="file" name="l_picfile"></td>
		</tr>
		<tr>
			<td align="right"{error_l_password}><b>Parole:</b></td>
			<td><input type="password" name="data[l_password]"></td>
		</tr>
		<tr>
			<td align="right"{error_l_password} nowrap><b>Parole 2x:</b></td>
			<td><input type="password" name="data[l_password2]"></td>
		</tr>
		<tr>
			<td class="TD-cat" colspan="2">Forums</td>
		</tr>
		<tr>
			<td colspan="2"{error_l_forumsort_themes}><b>Tēmas kārtot pēc:</b></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="radio" name="data[l_forumsort_themes]" value="T"{l_forumsort_themes_T}> tēmu datumiem
				<input type="radio" name="data[l_forumsort_themes]" value="C"{l_forumsort_themes_C}> jaunākā komentāra
			</td>
		</tr>
		<tr>
			<td colspan="2"{error_l_forumsort_msg}><b>Ziņojumus kārtot:</b></td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="radio" name="data[l_forumsort_msg]" value="A"{l_forumsort_msg_A}> pēc datuma augoši
				<input type="radio" name="data[l_forumsort_msg]" value="D"{l_forumsort_msg_D}> pēc datuma dilstoši
			</td>
		</tr>
		<tr>
			<td colspan="2"><input id="l_disable_bobi" type="checkbox" name="data[l_disable_bobi]"{l_disable_bobi_checked}><label for="l_disable_bobi">Nerādīt bobijsxxx komentārus</label></td>
		</tr>
		<tr>
			<td colspan="2"><input id="l_disable_avatars" type="checkbox" name="data[l_disable_avatars]"{l_disable_avatars_checked}><label for="l_disable_avatars">Nerādīt avatārus</label></td>
		</tr>
		<tr>
			<td class="TD-cat" colspan="2">Palīdzība</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<p>*) Lai nomainītu paroli, tā jāievada abos lauciņos (ja negrib nomainīt - jāatstājs tukšs)</p>
				<p>*) Parole datubāzē tiek glabāta šifrēta ar vienvirziena algoritmu - tātad nevienam neizlasāma.</p>
				<p>*) Ja tiks mainīts e-pasts, konts kļūs neaktīvs, kamēr tas netiks apstiprināts tāpat kā reģistrējoties (kods tiks saņemts uz jauno e-pastu)</p>
				<p>*) Bildes izmēri nedrīksts pārsniegt {user_pic_w} x {user_pic_h}. Ja ja kāda no dimensijām pārsniegs dotos izmērus, tā tiks automātiski samazināta. Mazā bilde tiek ģenerēta automātiski (ne lielāka par {user_pic_tw} x {user_pic_th}). Pieņemti tiek faili 'image/gif', 'image/jpeg', 'image/pjpeg'</p>
				<p>*) Aizliegts pievienot XXX, LV likumdošanai neatbildstošas, citu cilvēku bildes un tamldz. drazu!</p>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value=" OK "></td>
		</tr>
		</form>
		<!-- END BLOCK_private_profile -->
		</table>
	</td>
</tr>
</table>
<!-- END BLOCK_profile -->
