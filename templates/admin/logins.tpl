<!-- BEGIN BLOCK_logins_list disabled -->
<div class="TD-cat-active">
	Logini: saraksts
</div>

<form action="" method="post" id="logins_list">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="hidden" name="logins_count" value="{logins_count}" />
		<input type="checkbox" name="logins_check_all" onclick="Truemetal.checkAll(this);" />
	</td>
	<td class="TD-cat">Logins</td>
	<td class="TD-cat">Niks</td>
	<td class="TD-cat">Pievienojies</td>
</tr>
<!-- BEGIN BLOCK_logins -->
<tr>
	<td class="{l_color_class}">
		<input type="hidden" name="l_id{logins_nr}" value="{l_id}" />
		<input type="checkbox" name="l_checked{logins_nr}" />
	</td>
	<td class="{l_color_class}" valign="top">
		<a href="{module_root}/{l_id}/">{l_login}</a>
	</td>
	<td class="{l_color_class}" valign="top">{l_nick}</td>
	<td class="{l_color_class}" valign="top">{l_entered}</td>
</tr>
<!-- END BLOCK_logins -->
<tr>
	<td colspan="5">
		Iezīmētos:
		<select name="action">
			<option value="">---</option>
			<option value="activate_multiple">Aktivizēt</option>
			<option value="deactivate_multiple">Deaktivizēt</option>
			<option value="delete_multiple">Dzēst</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_logins_list -->


<!-- BEGIN BLOCK_nologins disabled -->
<div>
Nav neviena logina
</div>
<!-- END BLOCK_nologins -->

<!-- BEGIN BLOCK_logins_error disabled -->
<span class="error-msg">{error_msg}</span>
<a href="#" onClick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_logins_error -->

<!-- BEGIN BLOCK_login_edit disabled -->
<form action="" method="post">
<div class="TD-cat-active">
	Logini: rediģēt <em>{l_login}</em>
</div>
<table class="Main">
<tr>
	<td class="right nowrap">ID:</td>
	<td>{l_id}</td>
</tr>
<tr>
	<td class="right nowrap">Login:</td>
	<td><input type="text" name="data[l_login]" value="{l_login}" size="16" /></td>
</tr>
<tr>
	<td class="right nowrap">Niks:</td>
	<td><input type="text" name="data[l_nick]" value="{l_nick}" size="16" /></td>
</tr>
<tr>
	<td class="right nowrap">E-pasts:</td>
	<td><input type="text" name="data[l_email]" value="{l_email}" size="64" /></td>
</tr>
<tr>
	<td class="right nowrap">Aktīvs?:</td>
	<td>
		<select name="data[l_active]">
			<option value="Y"{l_active_Y_sel}>Jā</option>
			<option value="N"{l_active_N_sel}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Akceptēts?:</td>
	<td>
		<select name="data[l_accepted]">
			<option value="N"{l_accepted_N_sel}>Nē</option>
			<option value="Y"{l_accepted_Y_sel}>Jā</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">E-pasts redzams?:</td>
	<td>
		<select name="data[l_emailvisible]">
			<option value="Y"{l_emailvisible_Y_sel}>Jā</option>
			<option value="N"{l_emailvisible_N_sel}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Ielogojies?:</td>
	<td>
		<select name="data[l_logedin]">
			<option value="N"{l_logedin_N_sel}>Nē</option>
			<option value="Y"{l_logedin_Y_sel}>Jā</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Ievadīts:</td>
	<td><input type="text" name="data[l_entered]" value="{l_entered}" size="20" /></td>
</tr>
<tr>
	<td class="right nowrap">Pēdējoreiz manīts:</td>
	<td><input type="text" name="data[l_lastaccess]" value="{l_lastaccess}" size="20" /></td>
</tr>
<tr>
	<td class="right nowrap">IP:</td>
	<td>{l_userip}</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value=" Saglabāt " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_login_edit -->






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
