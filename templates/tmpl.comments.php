<script type="text/javascript">
function artcomVote(acId, value)
{
	Vote('artcom', acId, value, '#artcomVotes' + acId);
} // artcomVote

</script>
<table width="100%" cellpadding="2" cellspacing="0" border="0" align="center" bgcolor="#444444">
	<tr>
		<td width="100%" class="TD-cat"><b>{art_name}</b></td>
	</tr>
	<tr>
		<td width="100%">{art_data}</td>
	</tr>
	<tr>
		<td width="100%" bgcolor="black">Ievietots: {art_date}</td>
	</tr>
</table>

<a name="comments"></a>
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
<tr>
	<td colspan="6" class="TD-cat">Komentāri:</td>
</tr>
<!-- BEGIN BLOCK_comment disabled -->
<tr>
	<td class="TD-forum-cat" width="100%"><a name="comment{c_id}"></a><small><b>{c_username}</b><!-- BEGIN BLOCK_email disabled -->, <a href="mailto:{c_useremail}" class="A-small">{c_useremail}</a><!-- END BLOCK_email -->, {c_date}</small></td>
	<!-- BEGIN BLOCK_article_comment_vote disabled -->
	<td class="TD-forum-cat {comment_vote_class}" id="artcomVotes{c_id}">{c_votes}</td>
	<td class="TD-forum-cat"><a href="{http_root}/vote/up/artcom/{c_id}/" title="Ōjā!" onclick="artcomVote('{c_id}', 'up'); return false;">[+]</a></td>
	<td class="TD-forum-cat"><a href="{http_root}/vote/down/artcom/{c_id}/" title="Dauns..." onclick="artcomVote('{c_id}', 'down'); return false;">[&ndash;]</a></td>
	<!-- END BLOCK_article_comment_vote -->
	<td class="TD-forum-cat"><!-- BEGIN BLOCK_profile_link disabled --><a href="{http_root}/profile/user/{user_login_id}/" onclick="pop('{http_root}/profile/user/{user_login_id}/', 400, 400, 'profile{user_login_id}'); return false;">[Profils]</a><!-- END BLOCK_profile_link --></td>
	<td class="TD-forum-cat"><a href="{module_root}/{art_id}/#comment{c_id}">[Link]</a></td>
</tr>
<tr>
	<td colspan="6" width="100%" bgcolor="#444444">{c_datacompiled}</td>
</tr>
<tr>
	<td colspan="6" width="100%">&nbsp;</td>
</tr>
<!-- END BLOCK_comment -->
</table>

<!-- BEGIN BLOCK_nocomment disabled -->
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
<tr>
	<td width="100%">Šim rakstam nav neviena komentāra!</td>
</tr>
</table>
<!-- END BLOCK_nocomment -->

<!-- BEGIN BLOCK_notloggedin disabled -->
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center" bgcolor="#330000">
	<tr>
		<td width="100%"><br><br>Komentārus rakstīt var tikai reģistrēti lietotāji, tapēc, ielogojies vai <a href="{http_root}/register/">reģistrējies</a>!<br><br><br><br></td>
	</tr>
</table>
<!-- END BLOCK_notloggedin -->

<!-- BEGIN BLOCK_loggedin disabled -->
<form action="{module_root}/{art_id}/#add_comments" method="post">
<input type="hidden" name="action" value="add_comment">
<input type="hidden" name="c_referrer" value="{c_referrer}">
<a name="add_comments"></a>
<table width="100%" cellpadding="2" cellspacing="0" align="center">
<tr>
	<td colspan="2" class="TD-cat">Pievienot komentāru:</td>
</tr>
<!-- BEGIN BLOCK_comment_error disabled -->
<tr>
	<td colspan="2" class="error">{error_msg}</td>
</tr>
<!-- END BLOCK_comment_error -->
<tr>
	<td align="right">Vārds:</td>
	<td width="100%">{acd_username}</td>
</tr>
<tr>
	<td colspan="2" valign="top">Ziņa:</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;"><textarea name="data[c_data]" cols="50" rows="10" style="width: 100%;"></textarea></td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;"><input type="submit" value="Pievienot">&nbsp;<input type="button" value="Atcelt" onClick="location.replace('{http_root}/')"></td>
</tr>
</table>
</form>
<!-- END BLOCK_loggedin -->
