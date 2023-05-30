<div class="TD-cat-active">
	/ <a class="A-cat" href="{module_root}/">Forums</a><!-- BEGIN BLOCK_forum_path disabled --> / <a class="A-cat" href="/admin/{forum_path}">{forum_name}</a><!-- END BLOCK_forum_path --> / {res_name}
</div>

<!-- BEGIN BLOCK_forum_edit disabled -->
<form action="" method="post" id="forum_edit">
<table class="Main">
<tr>
	<td class="right nowrap">Res ID:</td>
	<td>{res_id}</td>
</tr>
<tr>
	<td class="right nowrap">Nosaukums:</td>
	<td>
		<input type="hidden" name="action" value="save_forum">
		<input type="hidden" name="forum[forum_id]" value="{forum_id}">
		<input type="text" name="res[res_name]" value="{res_name}" size="64">
	</td>
</tr>
<tr>
	<td class="right nowrap">Tips</td>
	<td>
		<select name="forum[type_id]">
		<!-- BEGIN BLOCK_forum_type_list -->
		<option value="{type_id}"{type_id_selected}>{type_name}</option>
		<!-- END BLOCK_forum_type_list -->
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Pasākuma sākums:</td>
	<td>
		<input type="text" name="forum[event_startdate]" value="{event_startdate}">
	</td>
</tr>
<tr>
	<td class="right nowrap"></td>
	<td>
		<label><input type="checkbox" value="1" name="res[res_visible]"{res_visible1}> aktīvs</label>
	</td>
</tr>
<tr>
	<td class="right nowrap"></td>
	<td>
		<label><input type="checkbox" value="1" name="forum[forum_allow_childs]"{forum_allow_childs1}> var būt apakštēmas</label>
	</td>
</tr>
<tr>
	<td class="right nowrap"></td>
	<td>
		<label><input type="checkbox" value="1" name="forum[forum_closed]"{forum_closed1}> tēma slēgta</label>
	</td>
</tr>
<tr>
	<td class="right nowrap">Ievadīts:</td>
	<td>
		<input type="text" name="res[res_entered]" value="{res_entered}">
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
		<select name="forum[forum_display]">
			<option value="0"{forum_display0}>Data Compiled</option>
			<option value="1"{forum_display1}>Data</option>
		</select>
	</td>
</tr>
<tr>
	<td class="right nowrap">Rādīt arī zem</td>
	<td>
		<select name="forum[forum_modid]">
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
		<textarea id="res_data" name="res[res_data]" rows="15" cols="150">{res_data}</textarea>
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
	<td><input type="text" name="res[res_nickname]" value="{res_nickname}"></td>
</tr>
<tr>
	<td class="right nowrap">Lietotāja e-pasts:</td>
	<td><input type="text" name="res[res_email]" value="{res_email}"></td>
</tr>
<tr>
	<td class="right nowrap">Lietotāja IP:</td>
	<td><input type="text" name="res[res_ip]" value="{res_ip}"></td>
</tr>
<tr>
	<td colspan="2">
		<input type="submit" value="Saglabāt">
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
		<input type="hidden" name="action" value="add_forum">
		Jauna tēma
	</td>
</tr>
<tr>
	<td>Nosaukums:</td>
	<td><input type="text" name="res[res_name]" maxlength="32" size="20"></td>
	<td><input type="submit" value="Pievienot"></td>
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
	<td class="TD-cat"><input type="checkbox" name="forum_check_all" onclick="Truemetal.checkAll(this);"></td>
	<td colspan="3" class="TD-cat">Tēmas</td>
</tr>
<!-- BEGIN BLOCK_forum_theme_item -->
<tr>
	<td class="{forum_color_class}">
		<input type="checkbox" name="forum_checked[{forum_id}]">
	</td>
	<td class="{forum_color_class}" style="white-space: nowrap;">
		{forum_padding}<a href="{module_root}/{forum_id}/">{res_name}</a>
	</td>
	<td class="{forum_color_class}">{res_child_count}</td>
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
		<input type="submit" value="  OK  ">
	</td>
</tr>
</table>
</form>
<!-- END BLOCK_forum_themes -->

<!-- BEGIN BLOCK_forum_comments --><!-- END BLOCK_forum_comments -->

