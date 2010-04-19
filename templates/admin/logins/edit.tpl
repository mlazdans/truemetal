<!-- BEGIN BLOCK_login_edit -->
<form action="" method="post">
<div class="TD-cat-active">
	<input type="hidden" name="action" value="save" />
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
	<td class="right nowrap">Manīts no:</td>
	<td>
		<a href="{http_root}/admin/reports/?report=ip&amp;ips={all_ips}">{all_ips_view}</a>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value=" Saglabāt " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_login_edit -->

<!-- BEGIN BLOCK_logins_also disabled -->
<div class="TD-cat-active">
	IP adrese manīta arī šādiem fruktiem
</div>
<table class="Main">
<!-- BEGIN BLOCK_logins_also_list -->
<tr>
	<td class="{l_color_class}">
		<a href="{http_root}/admin/logins/{l_id}/" style="font-weight: bold;">{l_login}</a>
	</td>
	<td class="{l_color_class}">{l_nick}</td>
	<td class="{l_color_class}">{comment_count}</td>
</tr>
<!-- END BLOCK_logins_also_list -->
</table>
<!-- END BLOCK_logins_also -->

<!-- BEGIN BLOCK_login_comments --><!-- END BLOCK_login_comments -->

