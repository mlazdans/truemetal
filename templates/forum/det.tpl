<div class="TD-cat">{res_name}</div>
<!-- BEGIN BLOCK_attend --><!-- END BLOCK_attend -->
<div class="profile-header">
	<div class="user-info">
		<div class="nick">{res_nickname},</div>
		<div class="date">{res_date}</div>
	</div>

	<div class="controls">
		<div class="vote unselectable {comment_vote_class}" id="votes-{res_id}">
			{res_votes}
		</div>

		<!-- BEGIN BLOCK_vote_control disabled -->
		<div class="unselectable">
			<a href="/vote/up/{res_id}/" class="SendVote" data-res_id="{res_id}" data-vote="up">[&plus;]</a>
		</div>
		<div class="unselectable">
			<a href="/vote/down/{res_id}/" class="SendVote" data-res_id="{res_id}" data-vote="down">[&ndash;]</a>
		</div>
		<!-- END BLOCK_vote_control -->

		<!-- BEGIN BLOCK_profile_link disabled -->
		<div class="unselectable">
			<a href="/user/profile/{l_hash}/" class="ProfilePopup" data-hash="{l_hash}">[Profils]</a>
		</div>
		<!-- END BLOCK_profile_link -->
	</div>
</div>
<div class="res-data{c_disabled_user_class}">
	{res_data_compiled}
</div>

<!-- BEGIN BLOCK_forum_error disabled -->
<div class="error">{error_msg}</div>
<!-- END BLOCK_forum_error -->

<div class="List-sep"></div>

<div class="TD-cat" id="theme-comments-{forum_id}">
	Komentāri
</div>

<div class="List-item">
	<!-- BEGIN BLOCK_info_sort_A disabled -->
	Komentāri sakārtoti pēc to ievadīšanas datuma
	<!-- END BLOCK_info_sort_A -->

	<!-- BEGIN BLOCK_info_sort_D disabled -->
	Komentāri sakārtoti pēc to ievadīšanas datuma dilstoši
	<!-- END BLOCK_info_sort_D -->
</div>

<!-- BEGIN BLOCK_forum_comments --><!-- END BLOCK_forum_comments -->

<!-- BEGIN BLOCK_forum_closed disabled -->
<div class="Info">
	Tēma slēgta
</div>
<!-- END BLOCK_forum_closed -->

