<!-- BEGIN BLOCK_articles_list disabled -->
<div class="TD-cat-active">
	Ziņas: saraksts
</div>

<form action="" method="post">
<table class="Main">
<tr>
	<td valign="top">
		<input type="hidden" name="action" value="art_new" />
		<input type="submit" name="do" value="Jauns" />
	</td>
	<td valign="top">
		<input type="button" value="Atcelt" onclick="location.replace('{http_root}/admin/article/')" />
	</td>
	<td valign="top">&nbsp;</td>
</tr>
</table>
</form>

<!-- BEGIN BLOCK_noarticles disabled -->
<div>
	Nav nevienas ziņas
</div>
<!-- END BLOCK_noarticles -->

<!-- BEGIN BLOCK_articles disabled -->
<form action="" method="post" id="article_list">
<table class="Main">
<tr>
	<td class="TD-cat">
		<input type="hidden" name="article_count" value="{article_count}" />
		<input type="checkbox" name="article_check_all" onclick="Truemetal.checkAll(this);" />
	</td>
	<td class="TD-cat">Modulis</td>
	<td class="TD-cat">Nosaukums</td>
</tr>
<!-- BEGIN BLOCK_article_item -->
<tr>
	<td class="{art_color_class}">
		<input type="hidden" name="art_id{article_nr}" value="{art_id}" />
		<input type="checkbox" name="art_checked{article_nr}" />
	</td>
	<td class="{art_color_class}">{module_id}</td>
	<td class="{art_color_class}">
		<a href="{module_root}/{art_id}/">{art_name}</a>
	</td>
</tr>
<!-- END BLOCK_article_item -->
<tr>
	<td colspan="3">
		Iezīmētos:
		<select name="action">
			<option value="">---</option>
			<option value="delete_multiple">Dzēst</option>
			<option value="activate_multiple">Aktivizēt</option>
			<option value="deactivate_multiple">Deaktivizēt</option>
			<option value="show_multiple">Parādīt</option>
			<option value="hide_multiple">Slēpt</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_articles -->
<!-- END BLOCK_articles_list -->




<!-- BEGIN BLOCK_article_error disabled -->
<span class="error-msg">{error_msg}</span>
<a href="#" onclick="javascript:history.back()">Atpakaļ</a>
<!-- END BLOCK_article_error -->



<!-- BEGIN BLOCK_article_edit disabled -->

<!-- BEGIN BLOCK_editor_init --><!-- END BLOCK_editor_init -->

<div class="TD-cat-active">
	Ziņas: rediģēt <em>{art_name_edit}</em>
</div>

<form action="{module_root}/{art_id}/save" method="post" id="article_edit">
<table class="Main">
<tr>
	<th>Zem</th>
	<td>
		<input type="hidden" name="action" value="art_save" />
		<select name="data[art_modid]">
		<!-- BEGIN BLOCK_modules_under_list -->
		<option value="{mod_id}">{module_padding}{module_name}</option>
		<!-- END BLOCK_modules_under_list -->
		</select>
	</td>
</tr>
<tr>
	<th>Nosaukums</th>
	<td>
		<input type="text" name="data[art_name]" value="{art_name}" size="128" />
	</td>
</tr>
<tr>
	<th>Aktīvs?</th>
	<td>
		<select name="data[art_active]">
			<option value="N"{art_active_n}>Nē</option>
			<option value="Y"{art_active_y}>Jā</option>
		</select>
	</td>
</tr>
<tr>
	<th>Ievadīšanas datums</th>
	<td>
		<input type="text" name="data[art_entered]" value="{art_entered}" size="20" />
	</td>
</tr>
<tr>
	<th>Komentāri?</th>
	<td>
		<select name="data[art_comments]">
			<option value="Y"{art_comments_y}>Jā</option>
			<option value="N"{art_comments_n}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<th>Tips</th>
	<td>
		<select name="data[art_type]">
			<option value="O"{art_type_o}>Atvērts</option>
			<option value="R"{art_type_r}>Reģistrētiem</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value=" Saglabāt " />
	</td>
</tr>
<tr>
	<td colspan="2">Intro</td>
</tr>
<tr>
	<td colspan="2">
		<textarea class="edit" name="data[art_intro]" rows="15" cols="150">
			{art_intro}
		</textarea>
	</td>
</tr>
<tr>
	<td colspan="2">Teksts</td>
</tr>
<tr>
	<td colspan="2">
		<textarea class="edit" name="data[art_data]" rows="35" cols="150">
			{art_data}
		</textarea>
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_article_edit -->

<!-- BEGIN BLOCK_article_comments --><!-- END BLOCK_article_comments -->

