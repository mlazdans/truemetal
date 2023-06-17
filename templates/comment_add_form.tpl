<!-- BEGIN BLOCK_not_logged disabled -->
<div class="Info">
	Komentārus rakstīt var tikai reģistrēti lietotāji tapēc <a href="/login/">ielogojies</a> vai
	<a href="/register/">reģistrējies</a>!
</div>
<!-- END BLOCK_not_logged -->

<!-- BEGIN BLOCK_comment_add_form disabled -->
<form action="#comment_form" method="post" id="comment_form">
<input type="hidden" name="action" value="add_comment">
<table width="100%" cellpadding="2" cellspacing="0">
<!-- BEGIN BLOCK_comment_error disabled -->
<tr>
	<td colspan="2" class="error">{error_msg}</td>
</tr>
<!-- END BLOCK_comment_error -->
<tr>
	<td align="right">Vārds:</td>
	<td style="width: 100%">{USER_l_nick}</td>
</tr>
<tr>
	<td colspan="2" valign="top">Ziņa:</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<textarea name="res_data" cols="50" rows="15" style="width: 100%;">{res_data}</textarea>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<input type="submit" value=" Pievienot " class="DisableOnSubmit">
	</td>
</tr>
</table>
</form>

<div>
	<ul>
		<li>Maksimālais vārda garums: 36</li>
		<li>Maksimālais komentāra garums: 1600 vārdi</li>
		<li>Lai links kļūtu &quot;spiežams&quot;, tam priekšā ir jāliek <strong>https://</strong></li>
		<li>Lai ieliktu video no Youtube, vajag iekopēt <i>share</i> linku <strong>(nevis &lt;object&gt;)</strong>, piemēram:
			<ul>
				<li>https://www.youtube.com/watch?v=DB_8sxghxis</li>
				<li>https://www.youtube.com/watch?v=EwTZ2xpQwpA</li>
				<li>https://youtu.be/_SjGW-TJ4QE</li>
			</ul>
		</li>
		<li>Tirgojoties obligāti jānorāda cena</li>
	</ul>
</div>
<!-- END BLOCK_comment_add_form -->
