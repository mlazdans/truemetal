<!-- BEGIN BLOCK_comment disabled -->
<div class="Comment" id="comment{c_id}">
	<div class="profile-header">
		<div class="user-info">
			<div class="nick">{res_nickname},</div>
			<div class="date">{res_date}</div>
		</div>

		<div class="controls">
			<!-- BEGIN BLOCK_comment_edit disabled -->
			<div class="unselectable">
				<a href="/comment/edit/{c_id}/">[labot]</a>
			</div>
			<!-- END BLOCK_comment_edit -->

			<!-- BEGIN BLOCK_comment_debug disabled -->
			<div class="unselectable">
				<a href="/res/debug/{res_id}/">[debug]</a>
			</div>
			<!-- END BLOCK_comment_debug -->

			<div class="vote unselectable {comment_vote_class}" id="votes-{res_id}" title="+{res_votes_plus_count} - {res_votes_minus_count}">
				{res_votes}
			</div>

			<!-- BEGIN BLOCK_comment_vote disabled -->
			<div class="unselectable">
				<a href="/vote/up/{res_id}/" class="SendVote" data-res_id="{res_id}" data-vote="up">[&plus;]</a>
			</div>
			<div class="unselectable">
				<a href="/vote/down/{res_id}/" class="SendVote" data-res_id="{res_id}" data-vote="down">[&ndash;]</a>
			</div>
			<!-- END BLOCK_comment_vote -->

			<!-- BEGIN BLOCK_profile_link disabled -->
			<div class="unselectable">
				<a href="/user/profile/{l_hash}/" class="ProfilePopup" data-hash="{l_hash}">[Profils]</a>
			</div>
			<!-- END BLOCK_profile_link -->

			<div class="unselectable">
				<a href="{res_route}">[#{comment_nr}]</a>
			</div>
		</div>
	</div>
	<div class="res-data{c_disabled_user_class}">
		{res_data_compiled}
	</div>
</div>
<!-- END BLOCK_comment -->

<!-- BEGIN BLOCK_no_comments disabled -->
<div class="Info">Šim resursam nav neviena komentāra!</div>
<!-- END BLOCK_no_comments -->
