<!-- BEGIN BLOCK_nogalleries disabled -->
<div class="Info">
	Diemžēl pagaidām šeit nav nevienas galerijas.
</div>
<!-- END BLOCK_nogalleries -->

<!-- BEGIN BLOCK_gallery_list disabled -->
<div style="margin-bottom: 20px;">
	<!-- BEGIN BLOCK_gallery_group -->
	<div id="{gal_jump_id}" class="TD-cat">
		{gg_name}
	</div>
	<div style="padding-left: 20px;">{gg_data}</div>
	<!-- END BLOCK_gallery_group -->

	<!-- BEGIN BLOCK_gallery_data -->
	<div id="gal{gal_id}" style="padding-left: 20px;">
		<a href="/gallery/{gal_id}/">{res_name}</a> {res_data}
	</div>
	<!-- END BLOCK_gallery_data -->
</div>
<!-- END BLOCK_gallery_list -->

<!-- BEGIN BLOCK_thumb_list disabled -->
<div class="TD-cat">
	<a href="/gallery/#{gal_jump_id}">Galerijas</a> / {res_name}
</div>
	<!-- BEGIN BLOCK_thumb -->
		<!-- BEGIN BLOCK_tr1 --><div style="text-align: center; margin-bottom: 1em;"><!-- END BLOCK_tr1 -->
		<div class="unselectable" style="display: inline-block;position: relative;left:0;padding:0; margin: 0 2px;">
			<div class="List-item" style="text-align: left;">
				<div class="vote {comment_vote_class} vote-value" style="display: inline-block;text-align: center; padding:0; border:none;">{res_votes}</div>
				Kom. (<div class="<!-- BEGIN BLOCK_comments_new disabled -->Comment-new<!-- END BLOCK_comments_new -->" style="display: inline-block;">{res_comment_count}</div>)
			</div>
			<a href="/gallery/view/{gd_id}/#pic-holder"><img src="{thumb_path}" alt="" class="img-thumb" width="120"></a>
		</div>
		<!-- BEGIN BLOCK_tr2 --></div><!-- END BLOCK_tr2 -->
	<!-- END BLOCK_thumb -->
<!-- END BLOCK_thumb_list -->

<!-- BEGIN BLOCK_image disabled -->
<div class="TD-cat" id="pic-holder">
	<a class="A-cat" href="/gallery/#{gal_jump_id}">Galerijas</a> /
	<a class="A-cat" href="/gallery/{gal_id}/">{res_name}</a>
</div>

<div class="profile-header">
	<div class="user-info">
		<div class="nick">{res_nickname},</div>
		<div class="date">{res_date}</div>
	</div>

	<div class="controls">
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
			<a href="{res_route}">[#]</a>
		</div>
	</div>
</div>
<div style="text-align: center;"><a href="/gallery/view/{gd_nextid}/#pic-holder"><img src="{image_path}" alt="Nākamā" width="500"></a></div>
<div style="text-align: center;">{gd_descr}</div>

<div class="TD-cat">
	Komentāri
</div>
<!-- BEGIN BLOCK_gallery_comments --><!-- END BLOCK_gallery_comments -->

<!-- END BLOCK_image -->

