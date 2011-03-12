<!-- BEGIN BLOCK_nocomments disabled -->
<div class="TD-cat">Komentāri</div>
<div>Nav neviena komentāra</div>
<!-- END BLOCK_nocomments -->

<!-- BEGIN BLOCK_comments disabled -->
<form action="" method="post" id="comments">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="checkbox" name="comment_check_all" onclick="Truemetal.checkAll(this)" />
	</td>
	<td colspan="6" class="TD-cat">
		Komentāri
	</td>
</tr>
<!-- BEGIN BLOCK_comment_item -->
<tr>
	<th class="{c_color_class}">
		<input type="checkbox" name="c_id[]" value="{c_id}" />
	</th>
	<th class="{c_color_class} nowrap"><a href="{c_origin_href}">{c_origin_name}</a></th>
	<th class="{c_color_class} nowrap"><a href="/admin/logins/{login_id}/">{c_username} ({c_userlogin})</a></th>
	<th class="{c_color_class}"><a href="/admin/reports/?report=ip&amp;ips={c_userip}">{c_userip}</a></th>
	<th class="{c_color_class} nowrap">{c_entered}</th>
	<th class="{c_color_class}">
		<!-- BEGIN BLOCK_c_visible disabled -->aktīvs<!-- END BLOCK_c_visible -->
		<!-- BEGIN BLOCK_c_invisible disabled -->neaktīvs<!-- END BLOCK_c_invisible -->
	</th>
	<th class="{c_color_class}"><a href="#" onclick="Admin.viewCommentVotes({res_id}); return false;">votes</a></th>
</tr>
<tr>
	<td></td>
	<td class="{c_color_class}" colspan="6">
		{c_datacompiled}
	</td>
</tr>
<!-- END BLOCK_comment_item -->
<tr>
	<td colspan="7">
		Iezīmētos: <select name="action">
		<option value="">---</option>
		<option value="comment_delete">Dzēst</option>
		<option value="comment_show">Aktivizēt</option>
		<option value="comment_hide">Deaktivizēt</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_comments -->

