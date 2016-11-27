<a name="comments"></a>
<!-- BEGIN BLOCK_comment disabled -->
<!-- BEGIN BLOCK_comment_list -->
<div class="Comment" id="comment{c_id}">
	<div class="header">
		<div class="nick">{c_username}</div>
		<!-- BEGIN BLOCK_email disabled -->
		, <a href="mailto:{c_useremail}" class="A-small">{c_useremail}</a>
		<!-- END BLOCK_email -->

		<div class="date">, {c_date}</div>

		<div class="link unselectable">
			<a href="#comment{c_id}">[#{comment_nr}]</a>
		</div>
		<!-- BEGIN BLOCK_profile_link disabled -->
		<div class="profile unselectable">
			<a href="/user/profile/{user_login_id}/" onclick="Truemetal.viewProfile('{user_login_id}'); return false;">[Profils]</a>
		</div>
		<!-- END BLOCK_profile_link -->
		<!-- BEGIN BLOCK_comment_vote disabled -->
			<div class="vote unselectable">
				<a href="/vote/down/{res_id}/" title="-" onclick="Truemetal.Vote('{res_id}', 'down', '#comment{c_id} .vote-value'); return false;">[&ndash;]</a>
			</div>
			<div class="vote unselectable">
				<a href="/vote/up/{res_id}/" title="+" onclick="Truemetal.Vote('{res_id}', 'up', '#comment{c_id} .vote-value'); return false;">[+]</a>
			</div>
			<div class="vote {comment_vote_class} vote-value unselectable" title="+{res_votes_plus_count} -{res_votes_minus_count}">
				{res_votes}
			</div>
		<!-- END BLOCK_comment_vote -->
		<div class="center unselectable">&nbsp;</div>
	</div>
	<div class="data{c_disabled_user_class}">
		{c_datacompiled}
	</div>
</div>
<!-- END BLOCK_comment_list -->
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
	<a href="/register/">reģistrējies</a>!
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
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
		<textarea name="data[c_data]" cols="50" rows="15" style="width: 100%;">{c_data}</textarea>
	</td>
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
<!-- END BLOCK_addcomment -->

