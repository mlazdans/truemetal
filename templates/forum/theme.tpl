<div class="TD-cat">Forums / {current_theme_name}</div>
<div class="TD-content">

<!-- BEGIN BLOCK_is_pages disabled -->
<div class="Forum-cat" style="margin-bottom: 0; display: flex;">
	<!-- BEGIN BLOCK_page_prev -->
	<div>
		<a href="{current_theme_route}/page/{prev_page_id}/"><img src="/img/left.png" alt="Vienu lapu atpakaļ"></a>
	</div>
	<!-- END BLOCK_page_prev -->

	<div class="Forum-Pager">
	<!-- BEGIN BLOCK_page -->{page_seperator}
		<!-- BEGIN BLOCK_page_switcher -->
			<a href="{current_theme_route}/page/{page_id}/"{page_style}>[{p_id}]</a>
		<!-- END BLOCK_page_switcher -->
	<!-- END BLOCK_page -->
	</div>

	<!-- BEGIN BLOCK_page_next -->
	<div>
		<a href="{current_theme_route}/page/{next_page_id}/"><img src="/img/right.png" alt="Vienu lapu uz priekšu"></a>
	</div>
	<!-- END BLOCK_page_next -->

</div>
<!-- END BLOCK_is_pages -->

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

<!-- BEGIN BLOCK_forum_themes disabled -->
<div class="forum-theme">
	<div class="forum-theme-main">
		<div class="forum-theme-name"><a href="{res_route}"><b>{res_name}</b></a></div>
		<div class="forum-theme-comment-count {comment_class}">({res_comment_count})</div>
		<div class="forum-theme-last-comment-date">{res_comment_last_date}</div>
	</div>
	<div class="forum-theme-info">
		<!-- BEGIN BLOCK_profile_link disabled -->
		<a href="/user/profile/{l_hash}/" class="ProfilePopup" data-hash="{l_hash}" style="color: white;">{res_nickname}</a>, {res_date}
		<!-- END BLOCK_profile_link -->
		<!-- BEGIN BLOCK_noprofile disabled -->
		{res_nickname}, {res_date}
		<!-- END BLOCK_noprofile -->
	</div>
</div>
<!-- END BLOCK_forum_themes -->

<!-- BEGIN BLOCK_noforum disabled -->
<div class="List-item">
	Pagaidām forumam nav nevienas tēmas!
</div>
<!-- END BLOCK_noforum -->

<div class="List-sep"></div>

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
