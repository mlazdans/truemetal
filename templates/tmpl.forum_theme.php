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

<!-- BEGIN BLOCK_forum_themes disabled -->
<table class="Forum-Theme" cellpadding="2" cellspacing="1">
<tr>
	<td class="Forum-cat">Tēma</td>
	<td class="Forum-cat">Kom.</td>
	<td class="Forum-cat">Uzsāka</td>
</tr>
<!-- BEGIN BLOCK_forum -->
<tr>
	<td class="Forum-Theme-name">
		<a href="{module_root}/{forum_id}/" title="Datums: {forum_date}"><b>{forum_name}</b></a>
	</td>
	<td class="Forum-Theme-childcount<!-- BEGIN BLOCK_comments_new disabled --> Comment-new<!-- END BLOCK_comments_new -->">
		{forum_comment_count}
	</td>
	<td class="Forum-Theme-username">
		{forum_username}
	</td>
</tr>
<!-- END BLOCK_forum -->
</table>
<!-- END BLOCK_forum_themes -->

<!-- BEGIN BLOCK_info_sort_T disabled -->
<div class="List-item">
	Tēmas sakārtotas pēc ievadīšanas datuma
</div>
<!-- END BLOCK_info_sort_T -->
<!-- BEGIN BLOCK_info_sort_C disabled -->
<div class="List-item">
	Tēmas sakārtotas pēc pēdējā komentāra datuma
</div>
<!-- END BLOCK_info_sort_C -->

<!-- BEGIN BLOCK_noforum disabled -->
<div class="List-item">
	Pagaidām forumam nav nevienas tēmas!
</div>
<!-- END BLOCK_noforum  -->

<div class="Forum-sep"></div>

<div class="TD-cat">
	Pievienot jaunu tēmu
</div>

<!-- BEGIN BLOCK_notloggedin disabled -->
<div class="Info">
	Pievienot jaunu tēmu var tikai reģistrēti lietotāji, tapēc, ielogojies vai <a href="{http_root}/register/">reģistrējies</a>!
</div>
<!-- END BLOCK_notloggedin -->

<!-- BEGIN BLOCK_loggedin disabled -->
<a name="add_theme"></a>
<form action="{module_root}/{forum1_id}/#add_theme" method="post">
<table class="Forum-Theme-form" cellpadding="2" cellspacing="1">
<tr>
	<td align="right">
		<input type="hidden" name="action" value="add_theme" />
		Vārds:
	</td>
	<td>{forum_username}</td>
</tr>
<tr>
	<td align="right"<!-- BEGIN BLOCK_forumname_error disabled --> class="error-form"<!-- END BLOCK_forumname_error -->>Jauna tēma:</td>
	<td><input style="width: 95%;" type="text" name="data[forum_name]" maxlength="64" size="64" value="{forum_name}" /></td>
</tr>
<tr>
	<td align="right" valign="top"<!-- BEGIN BLOCK_forumdata_error disabled --> class="error-form"<!-- END BLOCK_forumdata_error -->>Ziņa:</td>
	<td><textarea style="width: 95%;" name="data[forum_data]" cols="50" rows="10">{forum_data}</textarea></td>
</tr>
<tr>
	<td align="right">&nbsp;</td>
	<td><input type="submit" value="Pievienot" /></td>
</tr>
</table>
</form>
<!-- END BLOCK_loggedin -->

