<div class="TD-cat">Profils: {l_nick}{is_blocked}</div>
<table>
	<tr>
		<td rowspan="4" class="List-item text-nowrap" style="vertical-align: middle;">
			<!-- BEGIN BLOCK_nopicture disabled -->
				<div style="text-align: center; width: {user_pic_tw}px;">Bildes nav!</div>
			<!-- END BLOCK_nopicture -->
			<!-- BEGIN BLOCK_picture disabled -->
				<div
					class="loading"
					style="min-height: 50px; min-width: {user_pic_tw}px;"
				><a
					href="/user/viewimage/{l_hash}/"
					class="ProfileImage"
					data-hash="{l_hash}"
					data-nick="{l_nick}"
				><img src="/user/thumb/{l_hash}/" onload="$(this).parent().parent().removeClass('loading');" alt=""></a></div>
			<!-- END BLOCK_picture -->
		</td>
		<th class="List-item">Manīts:</th>
		<td class="List-item w-100">{l_lastaccess_f}{l_lastaccess_days}</td>
	</tr>
	<tr>
		<th class="List-item">Pievienojies:</th>
		<td class="List-item">{l_entered_f}</td>
	</tr>
	<tr>
		<th class="List-item">Komentāri:</th>
		<td class="List-item"><a href="/user/comments/{l_hash}/">{comment_count}</a></td>
	</tr>
	<tr>
		<th class="List-item">E-pasts:</th>
		<td class="List-item">
			<!-- BEGIN BLOCK_public_email disabled --><a href="mailto:{l_email}">{l_email}</a><!-- END BLOCK_public_email -->
			<!-- BEGIN BLOCK_public_email_invisible disabled --><div class="disabled">-nepublicēts e-pasts-</div><!-- END BLOCK_public_email_invisible -->
		</td>
	</tr>
</table>

<!-- BEGIN BLOCK_disable_comments disabled -->
<form method="post" action="/user/profile/{l_hash}/">
<input type="hidden" name="action" value="disable_comments">
<div class="List-item">
	<label for="disable_comments">
		<label><input
			type="checkbox"
			name="disable_comments"
			onclick="this.form.submit()"
			{disable_comments_checked}
		>Nerādīt šī lietotāja komentārus</label>
	</label>
	<!-- <input type="submit" value="OK"> -->
</div>
</form>
<!-- END BLOCK_disable_comments -->
