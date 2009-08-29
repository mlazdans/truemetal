<!-- BEGIN BLOCK_logins_list disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td class="TD-cat"><input type="checkbox" name="logins_check_all" onClick="checkAll(this.form, this)"></td>
		<td width="100%" class="TD-cat">Logini</td>
	</tr>
	<!-- BEGIN BLOCK_logins -->
	<input type="hidden" name="l_id{logins_nr}" value="{l_id}">
	<tr>
		<td class="{l_color_class}"><input type="checkbox" name="l_checked{logins_nr}"></td>
		<td class="{l_color_class}" valign="top"><a href="{module_root}/{l_id}/">{l_nick} {l_login}</a></td>
	</tr>
	<!-- END BLOCK_logins -->
	<tr>
		<td colspan="2">Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="activate_multiple">Aktivizēt</option>
		<option value="deactivate_multiple">Deaktivizēt</option>
		<option value="delete_multiple">Dzēst</option>
		</select>
		<input type="submit" value="  OK  ">
		</td>
	</tr>
</table>
<input type="hidden" name="logins_count" value="{logins_count}">
</form>
<!-- END BLOCK_logins_list -->
<!-- BEGIN BLOCK_nologins disabled -->Nav neviena logina<!-- END BLOCK_nologins -->

<!-- BEGIN BLOCK_logins_error disabled -->
<span class="error-msg">{error_msg}</span>
<a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_logins_error -->

<!-- BEGIN BLOCK_login_view disabled -->
<table cellpadding="0" cellspacing="1" border="0">
<tr>
	<td align="right">IP:</td><td>{l_userip}</td>
</tr>
<tr>
	<td align="right">Login:</td><td><input type="text" name="data[l_login]" value="{l_login}"></td>
</tr>
<tr>
	<td align="right">E-pasts:</td><td><input type="text" name="data[l_email]" value="{l_email}"></td>
</tr>
</table>
<!-- END BLOCK_login_view -->

<!-- BEGIN BLOCK_login_view_comments disabled -->
<form action="" method="post">
<table cellpadding="2" cellspacing="2" border="0">
<tr>
	<td class="TD-cat"><input type="checkbox" name="article_comments_check_all" onClick="checkAll(this.form, this)"></td>
	<td colspan="5" width="100%" class="TD-cat">Komentāri</td>
</tr>
<!-- BEGIN BLOCK_login_view_article_c disabled -->
<tr>
	<td colspan="6" class="TD-cat">Ziņas</td>
</tr>
<!-- BEGIN BLOCK_article_c -->
<!-- BEGIN BLOCK_login_article -->
<tr>
	<td title="Site">S:</td>
	<td colspan="5" class="TD-cat"><a href="{http_root}/article/{art_id}/" target="_blank">{art_name}</a></td>
</tr>
<tr>
	<td title="Admin">A:</td>
	<td colspan="5" class="TD-cat"><a href="{admin_root}/article/{art_id}/" target="_blank">{art_name}</a></td>
</tr>
<tr>
	<td nowrap></td>
	<td class="TD-cat" nowrap>Lietotājs</td>
	<td class="TD-cat" nowrap>E-pasts</td>
	<td class="TD-cat" nowrap>Ievadīts</td>
	<td class="TD-cat" nowrap>IP</td>
	<td class="TD-cat" width="100%"></td>
</tr>
<!-- END BLOCK_login_article -->
<tr>
	<td class="{comment_color_class}" nowrap><input type="checkbox" name="comment_checked{comment_nr}"></td>
	<td class="{comment_color_class}" nowrap><a href="{http_root}/article/{art_id}/#comment{ac_id}" target="_blank">{ac_username}</a></td>
	<td class="{comment_color_class}" nowrap>{ac_useremail}</td>
	<td class="{comment_color_class}" nowrap>{ac_entered}</td>
	<td class="{comment_color_class}" nowrap>{ac_userip}</td>
	<td class="{comment_color_class}" width="100%"></td>
</tr>
<tr>
	<td></td>
	<td colspan="5" class="{comment_color_class}">{ac_datacompiled}</td>
</tr>
<!-- END BLOCK_article_c -->
</table>
<!-- END BLOCK_login_view_article_c -->

<!-- BEGIN BLOCK_login_view_forum_c disabled -->
<table cellpadding="2" cellspacing="2" border="0">
<tr>
	<td colspan="6" class="TD-cat">Forums</td>
</tr>
<!-- BEGIN BLOCK_forum_c -->
<!-- BEGIN BLOCK_login_forum -->
<tr>
	<td title="Site">S:</td>
	<td colspan="5" class="TD-cat"><a href="{http_root}/forum/{u_forum_id}/" target="_blank">{u_forum_name}</a></td>
</tr>
<tr>
	<td title="Admin">A:</td>
	<td colspan="5" class="TD-cat"><a href="{admin_root}/forum/{u_forum_id}/" target="_blank">{u_forum_name}</a></td>
</tr>
<tr>
	<td nowrap></td>
	<td class="TD-cat" nowrap>Lietotājs</td>
	<td class="TD-cat" nowrap>E-pasts</td>
	<td class="TD-cat" nowrap>Ievadīts</td>
	<td class="TD-cat" nowrap>IP</td>
	<td class="TD-cat" width="100%"></td>
</tr>
<!-- END BLOCK_login_forum -->
<tr>
	<td class="{comment_color_class}"><input type="checkbox" name="comment_checked{comment_nr}"></td>
	<td class="{comment_color_class}" nowrap><a href="{http_root}/forum/{u_forum_id}/#comment{forum_id}" target="_blank">{forum_username}</a></td>
	<td class="{comment_color_class}" nowrap>{forum_useremail}</td>
	<td class="{comment_color_class}" nowrap>{forum_entered}</td>
	<td class="{comment_color_class}" nowrap>{forum_userip}</td>
	<td class="{comment_color_class}" width="100%"></td>
</tr>
<tr>
	<td></td>
	<td colspan="5" class="{comment_color_class}">{forum_datacompiled}</td>
</tr>
<!-- END BLOCK_forum_c -->
</table>
<!-- END BLOCK_login_view_forum_c -->
</form>
<!-- END BLOCK_login_view_comments -->