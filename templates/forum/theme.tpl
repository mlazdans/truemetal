<div class="TD-cat">{current_theme_name}</div>
<div class="TD-content">

<!-- BEGIN BLOCK_is_pages disabled -->
<div class="Forum-cat" style="margin-bottom: 0;">
	<!-- BEGIN BLOCK_page_prev -->
	<div class="Forum-Page-prev">
		<a href="{current_theme_route}/page/{prev_page_id}/"><img src="/img/left.png" alt="Pa kreisi"></a>
	</div>
	<!-- END BLOCK_page_prev -->

	<!-- BEGIN BLOCK_page_next -->
	<div class="Forum-Page-next">
		<a href="{current_theme_route}/page/{next_page_id}/"><img src="/img/right.png" alt="Pa labi"></a>
	</div>
	<!-- END BLOCK_page_next -->

	<div class="Forum-Pager">
	<!-- BEGIN BLOCK_page -->{page_seperator}
		<!-- BEGIN BLOCK_page_switcher -->
			<a href="{current_theme_route}/page/{page_id}/"{page_style}>[{p_id}]</a>
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
		<a href="{res_route}" title="Datums: {res_date}"><b>{res_name}</b></a>
	</td>
	<td class="Forum-Theme-childcount<!-- BEGIN BLOCK_comments_new disabled --> Comment-new<!-- END BLOCK_comments_new -->">
		{res_comment_count}
	</td>
	<td class="Forum-Theme-username">
		{res_nickname}
	</td>
</tr>
<!-- END BLOCK_forum -->
</table>
<!-- END BLOCK_forum_themes -->

<!-- BEGIN BLOCK_info_sort_T disabled -->
<div class="List-item">
	Tēmas sakārtotas pēc to ievadīšanas datuma
</div>
<!-- END BLOCK_info_sort_T -->
<!-- BEGIN BLOCK_info_sort_C disabled -->
<div class="List-item">
	Tēmas sakārtotas pēc to pēdējā komentāra datuma
</div>
<!-- END BLOCK_info_sort_C -->

<!-- BEGIN BLOCK_noforum disabled -->
<div class="List-item">
	Pagaidām forumam nav nevienas tēmas!
</div>
<!-- END BLOCK_noforum -->

<div class="Forum-sep"></div>

<!-- BEGIN BLOCK_not_logged disabled -->
<div class="Info">
	Pievienot jaunu tēmu var tikai reģistrēti lietotāji, tapēc, ielogojies vai <a href="/register/">reģistrējies</a>!
</div>
<!-- END BLOCK_not_logged -->

<!-- BEGIN BLOCK_logged disabled -->
<div class="TD-cat">
	Pievienot jaunu tēmu
</div>
<div class="List-item">
	Ņem vērā - stulbs tēmas nosaukums garantē tēmas izdzēšanu un daudz mīnusus!
</div>
<!-- BEGIN BLOCK_forumdata_bazar disabled -->
<div class="List-item">
	Tirgus sadaļā tēmas veidot var sākt, ja reģistrējies vismaz 10 dienas VAI (plusi-mīnusi) >= 10.
</div>
<!-- END BLOCK_forumdata_bazar -->
{forum_add_theme_form}
<!-- END BLOCK_logged -->

</div>
