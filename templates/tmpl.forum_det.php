<script type="text/javascript">
function forumVote(forumId, value)
{
	Vote('forum', forumId, value, '#forumVotes' + forumId);
} // forumVote
</script>
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
<tr>
	<td colspan="6" width="100%" class="TD-cat">Tēma: <b>{forum1_name}</b></td>
</tr>
<!-- BEGIN BLOCK_addressbar disabled -->
<tr>
	<td colspan="6" valign="top" width="100%" class="TD-forum-cat">
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
				<td width="100%"><a href="{module_root}/"><b>Forums</b></a><!-- BEGIN BLOCK_forum_path disabled --> / <a href="{module_root}/{forum1_path}">{forum1_name}</a><!-- END BLOCK_forum_path --></td>
			</tr>
		</table>
	</td>
</tr>
<!-- END BLOCK_addressbar -->
<!-- BEGIN BLOCK_info_sort_A disabled -->
<tr>
	<td colspan="6" width="100%" align="center">Ziņojumi sakārtoti pēc ievadīšanas datuma augoši.</td>
</tr>
<!-- END BLOCK_info_sort_A -->
<!-- BEGIN BLOCK_info_sort_D disabled -->
<tr>
	<td colspan="6" width="100%" align="center">Ziņojumi sakārtoti pēc ievadīšanas datuma dilstoši.</td>
</tr>
<!-- END BLOCK_info_sort_D -->
<!-- BEGIN BLOCK_forum disabled -->
<tr>
	<td width="100%" class="TD-forum-cat"><small><b><a name="comment{forum_id}"></a>{forum_username}</b><!-- BEGIN BLOCK_email disabled -->, <a href="mailto:{forum_useremail}" class="A-small">{forum_useremail}</a><!-- END BLOCK_email -->, {forum_date}</small></td>
	<!-- BEGIN BLOCK_forum_comment_vote disabled -->
	<td class="TD-forum-cat {comment_vote_class}" id="forumVotes{forum_id}">{forum_votes}</td>
	<td class="TD-forum-cat"><a href="{http_root}/vote/up/forum/{forum_id}/" title="Ōjā!" onclick="forumVote('{forum_id}', 'up'); return false;">[+]</a></td>
	<td class="TD-forum-cat"><a href="{http_root}/vote/down/forum/{forum_id}/" title="Dauns..." onclick="forumVote('{forum_id}', 'down'); return false;">[&ndash;]</a></td>
	<!-- END BLOCK_forum_comment_vote -->
	<td class="TD-forum-cat"><!-- BEGIN BLOCK_profile_link disabled --><a href="{http_root}/profile/user/{user_login_id}/" onclick="pop('{http_root}/profile/user/{user_login_id}/', 400, 400, 'profile{user_login_id}'); return false;">[Profils]</a><!-- END BLOCK_profile_link --></td>
	<td class="TD-forum-cat"><a href="{module_root}/{forum1_id}/#comment{forum_id}">[Link]</a></td>
</tr>
<tr>
	<td colspan="6" bgcolor="#444444" width="100%">
	<!-- BEGIN BLOCK_forum_avatar disabled -->
	<a
		href="/profile/view/{forum_userid}/"
		onclick="pop('/profile/view/{forum_userid}/', {pic_w}, {pic_h}); return false;"
	><img
		width="{avatar_w}"
		src="{avatar_path}"
		alt="Bilde: {forum_username}"
		border="0"
		align="left"
	></a>
	<!-- END BLOCK_forum_avatar -->
	{forum_datacompiled}
	</td>
</tr>
<tr>
	<td colspan="6" width="100%">&nbsp;</td>
</tr>
<!-- END BLOCK_forum -->
</table>

<!-- BEGIN BLOCK_noforum disabled -->
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
	<tr>
		<td colspan="5" width="100%">Pagaidām šai tēmai nav neviena komentāra!</td>
	</tr>
</table>
<!-- END BLOCK_noforum  -->

<!-- BEGIN BLOCK_notloggedin disabled -->
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center" bgcolor="#330000">
	<tr>
		<td width="100%"><br><br>Komentārus rakstīt var tikai reģistrēti lietotāji, tapēc, ielogojies vai <a href="{http_root}/register/">reģistrējies</a>!<br><br><br><br></td>
	</tr>
</table>
<!-- END BLOCK_notloggedin -->

<!-- BEGIN BLOCK_loggedin disabled -->
<form action="{module_root}/{forum1_id}/#add_comments" method="post">
<input type="hidden" name="action" value="add_item">
<a name="add_comments"></a>
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
<tr>
	<td colspan="2" nowrap class="TD-cat">Pievienot komentāru:</td>
</tr>
<tr>
	<td align="right">Vārds:</td>
	<td width="100%">{forumd_username}</td>
</tr>
<tr>
	<td colspan="2" valign="top"<!-- BEGIN BLOCK_forumdata_error disabled --> class="error"<!-- END BLOCK_forumdata_error -->>Ziņa:</td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;"><textarea style="width: 100%;" name="data[forum_data]" cols="50" rows="10"></textarea></td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;"><input type="submit" value="Pievienot"></td>
</tr>
<tr>
	<td colspan="2" style="padding-left: 16px; padding-right: 16px;">
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
	</td>
</tr>

</table>
</form>
<!-- END BLOCK_loggedin -->
