<a name="comments"></a>

<!-- BEGIN BLOCK_comment disabled -->
<table class="Comments" cellpadding="2" cellspacing="1">
<!-- BEGIN BLOCK_comment_list -->
<tr>
	<td class="Comment-cat" style="width: 100%;"><small><b>{c_username}</b><!-- BEGIN BLOCK_email disabled -->, <a href="mailto:{c_useremail}" class="A-small">{c_useremail}</a><!-- END BLOCK_email -->, {c_date}</small></td>
	<!-- BEGIN BLOCK_comment_vote disabled -->
	<td class="Comment-cat {comment_vote_class}" id="comment{c_id}">{c_votes}</td>
	<td class="Comment-cat"><a href="{http_root}/vote/up/{c_id}/" title="Ōjā!" onclick="Vote('{c_id}', 'up', '#comment{c_id}'); return false;">[+]</a></td>
	<td class="Comment-cat"><a href="{http_root}/vote/down/{c_id}/" title="Dauns..." onclick="Vote('{c_id}', 'down', '#comment{c_id}'); return false;">[&ndash;]</a></td>
	<!-- END BLOCK_comment_vote -->
	<td class="Comment-cat"><!-- BEGIN BLOCK_profile_link disabled --><a href="{http_root}/profile/user/{user_login_id}/" onclick="pop('{http_root}/profile/user/{user_login_id}/', 400, 400, 'profile{user_login_id}'); return false;">[Profils]</a><!-- END BLOCK_profile_link --></td>
	<td class="Comment-cat"><a href="#comment{c_id}">[Link]</a></td>
</tr>
<tr>
	<td class="Comment-data" colspan="6">{c_datacompiled}</td>
</tr>
<tr>
	<td colspan="6" class="Comment-sep"></td>
</tr>
<!-- END BLOCK_comment_list -->
</table>
<!-- END BLOCK_comment -->

<!-- BEGIN BLOCK_nocomment disabled -->
<div class="Info">
	Šim resursam nav neviena komentāra!
</div>
<!-- END BLOCK_nocomment -->

<div class="TD-cat">
	Pievienot komentāru
</div>

<!-- BEGIN BLOCK_notloggedin disabled -->
<div class="Info">
	Komentārus rakstīt var tikai reģistrēti lietotāji, tapēc ielogojies vai
	<a href="{http_root}/register/">reģistrējies</a>!
</div>
<!-- END BLOCK_notloggedin -->

<!-- BEGIN BLOCK_addcomment disabled -->

<form action="#add_comment" method="post" id="add_comment">
<table width="100%" cellpadding="2" cellspacing="0">
<!-- BEGIN BLOCK_comment_error disabled -->
<tr>
	<td colspan="2" class="error">{error_msg}</td>
</tr>
<!-- END BLOCK_comment_error -->
<tr>
	<td align="right">
		<input type="hidden" name="action" value="add_comment" />
		<input type="hidden" name="c_referrer" value="{c_referrer}" />
		Vārds:
	</td>
	<td style="width: 100%">{c_username}</td>
</tr>
<tr>
	<td colspan="2" valign="top">Ziņa:</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;"><textarea name="data[c_data]" cols="50" rows="10" style="width: 100%;"></textarea></td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<input type="submit" value="Pievienot" />
	</td>
</tr>
</table>
</form>

<div>
	<ul>
		<li>Maksimālais vārda garums: 25</li>
		<li>Maksimālais komentāra garums: 400 vārdi</li>
		<li>Lai links kļūtu &quot;spiežams&quot;, tam priekšā ir jāliek kāds no:
			<ul>
				<li>http(s?)://</li>
				<li>ftp://</li>
				<li>telnet://</li>
				<li>dchub://</li>
				<li>ed2k://</li>
				<li>mailto:</li>
				<li>callto:</li>
			</ul>
		</li>
		<li>Lai ieliktu video no Youtube, vajag iekpēt linku (nevis &lt;object&gt;), piemēram:
			<ul>
				<li>http://www.youtube.com/watch?v=DB_8sxghxis</li>
				<li>http://www.youtube.com/watch?v=EwTZ2xpQwpA</li>
			</ul>
		</li>
		<li>Stulbs tēmas nosaukums garantē tēmas izdzēšanu</li>
	</ul>
</div>
<!-- END BLOCK_addcomment -->

