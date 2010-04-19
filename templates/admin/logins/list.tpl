<div class="TD-cat-active">
	Logini: saraksts
</div>

<!-- BEGIN BLOCK_nologins disabled -->
<div>
Nav neviena logina
</div>
<!-- END BLOCK_nologins -->


<!-- BEGIN BLOCK_logins_search_from -->
<form action="" method="post" id="logins_search">
<div>
<input type="hidden" name="action" value="search" />
<label for="ls_q">IP;E-mail;Login;Nick: <input type="text" name="q" id="ls_q" value="{q}" /></label>
<label for="ls_l_active_n"><input type="checkbox" name="l_active_n" id="ls_l_active_n" {ls_l_active_n_checked}/> neaktīvie</label>
<label for="ls_l_accepted_n"><input type="checkbox" name="l_accepted_n" id="ls_l_accepted_n" {ls_l_accepted_n_checked}/> neakceptētie</label>
&nbsp;
<label for="ls_l_accepted_y"><input type="checkbox" name="l_accepted_y" id="ls_l_accepted_y" {ls_l_accepted_y_checked}/> akceptētie</label>
<label for="ls_l_active_y"><input type="checkbox" name="l_active_y" id="ls_l_active_y" {ls_l_active_y_checked}/> aktīvie</label>
<input type="submit" value="Meklēt"/>
</div>
</form>
<!-- END BLOCK_logins_search_from -->

<!-- BEGIN BLOCK_logins_list disabled -->
<form action="" method="post" id="logins_list">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="hidden" name="logins_count" value="{logins_count}" />
		<input type="checkbox" name="logins_check_all" onclick="Truemetal.checkAll(this);" />
	</td>
	<td class="TD-cat">Logins</td>
	<td class="TD-cat">Niks</td>
	<td class="TD-cat">Reitings</td>
	<td class="TD-cat">Komentāri</td>
	<td class="TD-cat">E-pasts</td>
	<td class="TD-cat" title="Aktīvs">Akt</td>
	<td class="TD-cat" title="Akceptēts">Akc</td>
	<td class="TD-cat">IP</td>
	<td class="TD-cat">Manīts</td>
	<td class="TD-cat">Pievienojies</td>
</tr>
<!-- BEGIN BLOCK_logins -->
<tr>
	<td class="{l_color_class}">
		<input type="hidden" name="l_id{logins_nr}" value="{l_id}" />
		<input type="checkbox" name="l_checked{logins_nr}" />
	</td>
	<td class="{l_color_class}">
		<a href="{module_root}/{l_id}/" style="font-weight: bold;">{l_login}</a>
	</td>
	<td class="{l_color_class}">{l_nick}</td>
	<td class="{l_color_class}">{votes}({votes_plus}-{votes_minus})</td>
	<td class="{l_color_class}">{comment_count}</td>
	<td class="{l_color_class}">
		<a href="mailto:{l_email}">{l_email}</a>
	</td>
	<td class="{l_color_class}">{l_active}</td>
	<td class="{l_color_class}">{l_accepted}</td>
	<td class="{l_color_class}" title="Atrast pēc IP">
		<a href="{http_root}/admin/reports/?report=ip&amp;ips={l_userip}">{l_userip}</a>
	</td>
	<td class="{l_color_class} nowrap">{l_lastaccess}</td>
	<td class="{l_color_class} nowrap">{l_entered}</td>
</tr>
<!-- END BLOCK_logins -->
<tr>
	<td colspan="10">
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

