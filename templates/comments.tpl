<!-- BEGIN BLOCK_comment disabled -->
<div class="Comment" id="comment{c_id}">
	<div class="profile-header">
		<div class="user-info">
			<div class="nick">{res_nickname},</div>
			<div class="date">{res_date}</div>
		</div>

		<div class="controls">
			<div class="vote unselectable {comment_vote_class}" id="votes-{res_id}" title="+{res_votes_plus_count} - {res_votes_minus_count}">
				{res_votes}
			</div>

			<!-- BEGIN BLOCK_comment_vote disabled -->
			<div class="unselectable">
				<a href="/vote/up/{res_id}/" class="SendVote" data-res_id="{res_id}" data-vote="up">[&plus;]</a>
			</div>
			<div class="unselectable">
				<a href="/vote/down/{res_id}/" class="SendVote" data-res_id="{res_id}" data-vote="down">[&ndash;]</a>
			</div>
			<!-- END BLOCK_comment_vote -->

			<!-- BEGIN BLOCK_profile_link disabled -->
			<div class="unselectable">
				<a href="/user/profile/{l_hash}/" class="ProfilePopup" data-hash="{l_hash}">[Profils]</a>
			</div>
			<!-- END BLOCK_profile_link -->

			<div class="unselectable">
				<a href="{res_route}">[#{comment_nr}]</a>
			</div>
		</div>
	</div>
	<div class="res-data{c_disabled_user_class}">
		{res_data_compiled}
	</div>
</div>
<!-- END BLOCK_comment -->

<!-- BEGIN BLOCK_nocomment disabled -->
<div class="Info">Šim resursam nav neviena komentāra!</div>
<!-- END BLOCK_nocomment -->

<!-- BEGIN BLOCK_addcomment -->
<div class="TD-cat">Pievienot komentāru</div>

<!-- BEGIN BLOCK_notloggedin disabled -->
<div class="Info">
	Komentārus rakstīt var tikai reģistrēti lietotāji, tapēc <a href="/login/">ielogojies</a> vai
	<a href="/register/">reģistrējies</a>!
</div>
<!-- END BLOCK_notloggedin -->

<!-- BEGIN BLOCK_comment_form disabled -->
<form action="#add_comment" method="post" id="add_comment">
<table width="100%" cellpadding="2" cellspacing="0">
<!-- BEGIN BLOCK_comment_error disabled -->
<tr>
	<td colspan="2" class="error">{error_msg}</td>
</tr>
<!-- END BLOCK_comment_error -->
<tr>
	<td align="right">
		<input type="hidden" name="action" value="add_comment">
		<input type="hidden" name="c_referrer" value="{c_referrer}">
		Vārds:
	</td>
	<td style="width: 100%">{USER_l_nick}</td>
</tr>
<tr>
	<td colspan="2" valign="top">Ziņa:</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<textarea name="data[c_data]" cols="50" rows="15" style="width: 100%;">{c_data}</textarea>
	</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<input type="submit" value="Pievienot">
	</td>
</tr>
</table>
</form>

<div>
	<ul>
		<li>Maksimālais vārda garums: 25</li>
		<li>Maksimālais komentāra garums: 400 vārdi</li>
		<li>Lai links kļūtu &quot;spiežams&quot;, tam priekšā ir jāliek <strong>http://</strong></li>
		<li>Lai ieliktu video no Youtube, vajag iekopēt linku <strong>(nevis &lt;object&gt;)</strong>, piemēram:
			<ul>
				<li>http://www.youtube.com/watch?v=DB_8sxghxis</li>
				<li>http://www.youtube.com/watch?v=EwTZ2xpQwpA</li>
				<li>http://youtu.be/_SjGW-TJ4QE</li>
			</ul>
		</li>
		<li>Tirgojoties obligāti jānorāda cena</li>
	</ul>
</div>
<!-- END BLOCK_comment_form -->
<!-- END BLOCK_addcomment -->

