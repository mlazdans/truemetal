<!-- BEGIN BLOCK_not_loged disabled -->
<div class="Info">
	TrueMetal!
</div>
<!-- END BLOCK_not_loged -->

<!-- BEGIN BLOCK_no_such_login disabled -->
<div class="Info">
	Šāds profils neeksitē vai ir bloķēts!
</div>
<!-- END BLOCK_no_such_login -->

<!-- BEGIN BLOCK_profile disabled -->
	<!-- BEGIN BLOCK_profile_title disabled -->
	<div class="TD-cat">
		Profils: {l_nick}
	</div>
	<!-- END BLOCK_profile_title -->

	<!-- BEGIN BLOCK_nopicture disabled -->
	<div class="List-item">
		Bildes nav!
	</div>
	<!-- END BLOCK_nopicture -->

	<!-- BEGIN BLOCK_picture disabled -->
	<div style="margin: 0 2px; float: left;">
		<a
			href="{module_root}/view/{l_login}/"
			onclick="Truemetal.viewProfileImage('{l_login}', {pic_w}, {pic_h}, '{l_nick}'); return false;"
		><img
			src="{pic_path}"
			alt=""
		/></a>
	</div>
	<!-- END BLOCK_picture -->

	<div class="List-item">
		<b>Manīts:</b> {l_lastaccess_f}{l_lastaccess_days}
	</div>

	<div class="List-item">
		<b>Pievienojies:</b> {l_entered_f}
	</div>

	<div class="List-item">
		<b>Komentāri:</b> {comment_count}
	</div>

	<!-- BEGIN BLOCK_public_email disabled -->
	<div class="List-item">
		<b>E-pasts:</b> <a href="mailto:{l_email}">{l_email}</a>
	</div>
	<!-- END BLOCK_public_email -->

	<!-- BEGIN BLOCK_disable_comments disabled -->
	<form method="post" action="/user/profile/{l_login}/">
	<input type="hidden" name="action" value="disable_comments" />
	<div class="List-item">
		<label for="disable_comments">
			<input type="checkbox" name="disable_comments" id="disable_comments"{disable_comments_checked} />
			Nerādīt šī lietotāja komentārus
		</label>
		<input type="submit" value="OK" />
	</div>
	</form>
	<!-- END BLOCK_disable_comments -->
<!-- END BLOCK_profile -->

