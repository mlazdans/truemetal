<div class="TD-cat">
	Forums: <b>{forum1_name}</b>
</div>

<div class="Forum-cat">
	<a href="{module_root}/"><b>Forums</b></a><!-- BEGIN BLOCK_forum_path disabled --> / <a href="{module_root}/{forum1_path}">{forum1_name}</a><!-- END BLOCK_forum_path -->
</div>

<!-- BEGIN BLOCK_is_pages disabled -->
<div class="Forum-cat">
	<!-- BEGIN BLOCK_page_prev -->
	<div class="Forum-Page-prev">
		<a href="{module_root}/{current_forum_id}/page/{prev_page_id}/"><img src="{http_root}/img/left.png" alt="Pa kreisi" /></a>
	</div>
	<!-- END BLOCK_page_prev -->

	<!-- BEGIN BLOCK_page_next -->
	<div class="Forum-Page-next">
		<a href="{module_root}/{current_forum_id}/page/{next_page_id}/"><img src="{http_root}/img/right.png" alt="Pa labi" /></a>
	</div>
	<!-- END BLOCK_page_next -->
	<div class="Forum-Pager">
	<!-- BEGIN BLOCK_page -->{page_seperator}
		<!-- BEGIN BLOCK_page_switcher -->
			<a href="{module_root}/{current_forum_id}/page/{page_id}/"{page_style}>[{page_id}]</a>
		<!-- END BLOCK_page_switcher -->
	<!-- END BLOCK_page -->
	</div>
</div>
<!-- END BLOCK_is_pages -->


<table cellpadding="2" cellspacing="1">
<tr>
	<td class="Forum-cat">Tēma</td>
	<td class="Forum-cat">Kom.</td>
	<td class="Forum-cat">Uzsāka</td>
</tr>
<!-- BEGIN BLOCK_forum disabled -->
<tr>
	<td class="Forum-Theme-name">
		<a href="{module_root}/{forum_id}/" title="Datums: {forum_date}"><b>{forum_name}</b></a>
	</td>
	<td class="Forum-Theme-childcount<!-- BEGIN BLOCK_comments_new disabled --> Comment-new<!-- END BLOCK_comments_new -->">
		{forum_childcount}
	</td>
	<td class="Forum-Theme-username">
		<!-- BEGIN BLOCK_email disabled -->
		<a href="mailto:{forum_useremail}" class="A-small">{forum_username}</a>
		<!-- END BLOCK_email -->
		<!-- BEGIN BLOCK_username disabled -->{forum_username}<!-- END BLOCK_username -->
	</td>
</tr>
<!-- END BLOCK_forum -->
</table>
{pages_bottom}

<!-- BEGIN BLOCK_info_sort_T disabled -->
<div class="List-item">
	Tēmas sakārtotas pēc ievadīšanas datuma.
</div>
<!-- END BLOCK_info_sort_T -->
<!-- BEGIN BLOCK_info_sort_C disabled -->
<div class="List-item">
	Tēmas sakārtotas pēc pēdējā komentāra datuma.
</div>
<!-- END BLOCK_info_sort_C -->

<!-- BEGIN BLOCK_noforum disabled -->
<div class="List-item">
	Pagaidām forumam nav nevienas tēmas!
</div>
<!-- END BLOCK_noforum  -->

<div class="Forum-sep"></div>

<!-- BEGIN BLOCK_forum_form disabled -->
<form action="{module_root}/{forum1_id}/#add_theme" method="post">
<input type="hidden" name="action" value="add_theme">
<a name="add_theme"></a>
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center">
	<tr>
		<td colspan="2" nowrap class="TD-cat">Pievienot jaunu tēmu:</td>
	</tr>
<!-- BEGIN BLOCK_notloggedin disabled -->
	<tr>
		<td>
<table width="100%" cellpadding="2" cellspacing="1" border="0" align="center" bgcolor="#330000">
	<tr>
		<td width="100%"><br><br>Pievienot jaunu tēmu var tikai reģistrēti lietotāji, tapēc, ielogojies vai <a href="{http_root}/register/">reģistrējies</a>!<br><br><br><br></td>
	</tr>
</table>
		</td>
	</tr>
<!-- END BLOCK_notloggedin -->
<!-- BEGIN BLOCK_loggedin disabled -->
	<tr>
		<td nowrap align="right">Vārds:</td>
		<td>{forumd_username}</td>
	</tr>
	<tr>
		<td nowrap align="right"<!-- BEGIN BLOCK_forumname_error disabled --> class="error-form"<!-- END BLOCK_forumname_error -->>Jauna tēma:</td>
		<td><input type="text" name="data[forum_name]" maxlength="64" size="64" value="{forumd_name}"></td>
	</tr>
	<tr>
		<td align="right" valign="top"<!-- BEGIN BLOCK_forumdata_error disabled --> class="error-form"<!-- END BLOCK_forumdata_error -->>Ziņa:</td>
		<td><textarea name="data[forum_data]" cols="50" rows="10">{forumd_data}</textarea></td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td width="100%"><input type="submit" value="Pievienot"></td>
	</tr>
<!-- END BLOCK_loggedin -->
</table>
</form>
<!-- END BLOCK_forum_form -->

