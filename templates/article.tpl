<div class="TD-cat">
	<div class="res-name"><a class="caption" href="{res_route}">{res_name}</a></div>
</div>

<div class="TD-content">

<div class="profile-header">
	<div class="user-info">
		<div class="nick">{res_nickname},</div>
		<div class="date">{res_date_short}</div>
	</div>

	<div class="controls">
		<div class="vote unselectable {comment_vote_class}" id="votes-{res_id}" title="+{res_votes_plus_count} - {res_votes_minus_count}">
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
		<div class="unselectable">
			<a href="{res_route}">[#]</a>
		</div>
	</div>
</div>

<div class="Article-item">
	<div class="data">
		<div class="intro">
		{res_intro}
		</div>
		{res_data}
	</div>
</div>
</div>

<div class="TD-cat" id="art-comments-{art_id}">Komentāri</div>
<div class="TD-content">{article_comments}</div>

<div class="TD-cat">Pievienot komentāru</div>
<div class="TD-content">{comment_add_form}</div>
