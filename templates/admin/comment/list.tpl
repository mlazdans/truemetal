<!-- BEGIN BLOCK_nocomments disabled -->
Nav neviena komentāra
<!-- END BLOCK_nocomments -->

<!-- BEGIN BLOCK_comments disabled -->
<form action="" method="post" id="comments">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="checkbox" name="comment_check_all" onclick="checkAll(this.form, this)" />
	</td>
	<td colspan="4" class="TD-cat">
		Komentāri
	</td>
</tr>
<!-- BEGIN BLOCK_comment_item -->
<tr>
	<th class="{c_color_class}">
		<input type="checkbox" name="c_id[]" value="{c_id}" />
	</th>
	<th class="{c_color_class} nowrap">{c_username} ({c_userlogin})</th>
	<th class="{c_color_class}">{c_userip}</th>
	<th class="{c_color_class} nowrap">{c_entered}</th>
	<th class="{c_color_class}">
		<!-- BEGIN BLOCK_c_visible disabled -->aktīvs<!-- END BLOCK_c_visible -->
		<!-- BEGIN BLOCK_c_invisible disabled -->neaktīvs<!-- END BLOCK_c_invisible -->
	</th>
</tr>
<tr>
	<td></td>
	<td class="{c_color_class}" colspan="4">
		{c_datacompiled}
	</td>
</tr>
<!-- END BLOCK_comment_item -->
<tr>
	<td colspan="5">
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

