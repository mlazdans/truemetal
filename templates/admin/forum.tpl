<div class="TD-cat-active">
	/ <a class="A-cat" href="{module_root}/">Forums</a><!-- BEGIN BLOCK_forum_path disabled --> / <a class="A-cat" href="{module_root}{forum1_path}">{forum1_name}</a><!-- END BLOCK_forum_path --> /
</div>

<!-- BEGIN BLOCK_forum_edit disabled -->
<div class="TD-cat-active">
	Labot forumu <em>{forum_name}</em>
</div>

<form action="" method="post" id="forum_edit">
<table class="Main">
<!-- BEGIN BLOCK_forum_resid disabled -->
<tr>
	<td class="right nowrap">Res ID:</td>
	<td>{res_id}</td>
</tr>
<!-- END BLOCK_forum_resid -->
<tr>
	<td class="right nowrap">Nosaukums:</td>
	<td>
		<input type="hidden" name="action" value="save_forum" />
		<input type="hidden" name="data[forum_id]" value="{forum_id}" />
		<input type="text" name="data[forum_name]" value="{forum_name}" size="64" />
	</td>
</tr>
<tr>
	<td class="right nowrap">Tips</td>
	<td>
		<select name="data[type_id]">
		<!-- BEGIN BLOCK_forum_type_list -->
		<option value="{type_id}"{type_id_selected}>{type_name}</option>
		<!-- END BLOCK_forum_type_list -->
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Pasākuma sākums:</td>
	<td>
		<input type="text" name="data[event_startdate]" value="{event_startdate}" />
	</td>
</tr>
<tr>
	<td class="right nowrap">Aktīvs?:</td>
	<td>
		<select name="data[forum_active]">
			<option value="Y"{forum_active_sel}>Jā</option>
			<option value="N"{forum_inactive_sel}>Nē</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Var būt apakštēmas?:</td>
	<td>
		<select name="data[forum_allowchilds]">
			<option value="N"{forum_prohibitchilds_sel}>Nē</option>
			<option value="Y"{forum_allowchilds_sel}>Jā</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Slēgts?:</td>
	<td>
		<select name="data[forum_closed]">
			<option value="N"{forum_open_sel}>Nē</option>
			<option value="Y"{forum_closed_sel}>Jā</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Ievadīts:</td>
	<td>
		<input type="text" name="data[forum_entered]" value="{forum_entered}" />
	</td>
</tr>
<!--
<tr>
	<td class="right nowrap"><label for="forum_showmainpage">Rādīt sākumlapā:</label></td>
	<td>
		<input id="forum_showmainpage" type="checkbox" name="data[forum_showmainpage]"{forum_showmainpage_checked}/>
	</td>
</tr>
-->
<tr>
	<td class="right nowrap">Rādīt:</td>
	<td>
		<select name="data[forum_display]">
			<option value="0"{forum_display_0_selected}>Data Compiled</option>
			<option value="1"{forum_display_1_selected}>Data</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Rādīt arī zem</td>
	<td>
		<select name="data[forum_modid]">
		<option value="" style="font-style: italic;">-nerādīt-</option>
		<!-- BEGIN BLOCK_modules_under_list -->
		<option value="{mod_id}"{module_selected}>{module_padding}{module_name}</option>
		<!-- END BLOCK_modules_under_list -->
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap" valign="top">Dati:</td>
	<td>
		<textarea id="forum_data" name="data[forum_data]" rows="15" cols="150">{forum_data}</textarea>
		<p><input
			type="button"
			value="Editor"
			onclick="
if($('#forum_data').hasClass('edit'))
{
	$('#forum_data').attr('rows', 15);
	tinyMCE.get('forum_data').remove();
	$('#forum_data').removeClass('edit');
} else {
	$('#forum_data').attr('rows', 25);
	$('#forum_data').addClass('edit');
	initEditor();
}"
		/></p>
	</td>
</tr>
<tr>
	<td class="right nowrap">Lietotāja vārds:</td>
	<td><input type="text" name="data[forum_username]" value="{forum_username}" /></td>
</tr>
<tr>
	<td class="right nowrap">Lietotāja e-pasts:</td>
	<td><input type="text" name="data[forum_useremail]" value="{forum_useremail}" /></td>
</tr>
<tr>
	<td class="right nowrap">Lietotāja IP:</td>
	<td><input type="text" name="data[forum_userip]" value="{forum_userip}" /></td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value="Saglabāt" />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_edit -->

<!-- BEGIN BLOCK_forum_theme_new disabled -->
<form action="" method="post" id="forum_theme_new">
<table class="Main">
<tr>
	<td colspan="3" class="TD-cat">
		<input type="hidden" name="action" value="add_forum" />
		Jauna tēma
	</td>
</tr>
<tr>
	<td>Nosaukums:</td>
	<td><input type="text" name="data[forum_name]" maxlength="32" size="20" /></td>
	<td><input type="submit" value="Pievienot" /></td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_theme_new -->

<!-- BEGIN BLOCK_forum_nothemes disabled -->
Nav nevienas tēmas
<!-- END BLOCK_forum_nothemes -->

<!-- BEGIN BLOCK_forum_themes disabled -->
<form action="" method="post" id="forum_themes">
<table class="Main">
<tr>
	<td class="TD-cat"><input type="checkbox" name="forum_check_all" onclick="Truemetal.checkAll(this);" /></td>
	<td colspan="3" class="TD-cat">Tēmas</td>
</tr>
<!-- BEGIN BLOCK_forum_theme_item -->
<tr>
	<td class="{forum_color_class}">
		<input type="checkbox" name="forum_checked[{forum_id}]" />
	</td>
	<td class="{forum_color_class}" style="white-space: nowrap;">
		{forum_padding}<a href="{module_root}/{forum_id}/">{forum_name}</a>
	</td>
	<td class="{forum_color_class}">{res_comment_count}</td>
	<td class="{forum_color_class}">
		<!-- BEGIN BLOCK_forum_inactive disabled -->neaktīvs<!-- END BLOCK_forum_inactive -->
		<!-- BEGIN BLOCK_forum_closed disabled -->slēgts<!-- END BLOCK_forum_closed -->
	</td>
</tr>
<!-- END BLOCK_forum_theme_item -->
<tr>
	<td colspan="4">
		Iezīmētos:
		<select name="action">
			<option value="">---</option>
			<option value="delete_multiple">Dzēst</option>
			<option value="activate_multiple">Aktivizēt</option>
			<option value="deactivate_multiple">Deaktivizēt</option>
			<option value="close_multiple">Slēgt</option>
			<option value="open_multiple">Atvērt</option>
		</select>
		<input type="submit" value="  OK  " />
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_themes -->

<!-- BEGIN BLOCK_forum_comments --><!-- END BLOCK_forum_comments -->

