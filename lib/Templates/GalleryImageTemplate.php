<?php declare(strict_types = 1);

class GalleryImageTemplate extends AbstractResTemplate
{
	public ViewResGdType $image;
	public ViewResGalleryType $gal;
	public bool $is_cache_enabled = true;

	protected function out(): void
	{
		$gal = $this->gal;
		$image = $this->image;
		$gd_id = $this->image->gd_id;

		if($this->is_cache_enabled && ($hash = cache_hash($gd_id."image.jpg")) && cache_exists($hash)){
			$image_path = cache_http_path($hash);
		} else {
			$image_path = "/gallery/image/$gd_id/";
		}

		# nechekojam, vai ir veel bildes
		$next_id = GalleryData::get_next_data($gal->res_id, $gd_id);
		$gd_nextid = $next_id ? $next_id : $gd_id;

		$gal_jump_id = "gg_".$gal->gal_ggid;

		?>
		<div class="TD-cat" id="pic-holder">
			<a class="A-cat" href="/gallery/#<?=$gal_jump_id ?>">Galerijas</a> /
			<a class="A-cat" href="/gallery/<?=$gal->gal_id ?>/"><?=specialchars($gal->res_name) ?></a>
		</div>

		<div class="profile-header">
			<div class="user-info">
				<div class="nick"><?=specialchars($gal->res_nickname) ?>,</div>
				<div class="date"><?=proc_date($gal->res_entered) ?></div>
			</div>

			<div class="controls">
				<div class="vote unselectable <?=$this->comment_vote_class ?>" id="votes-<?=$this->res_id ?>" title="+<?=$this->res_votes_plus_count ?> - <?=$this->res_votes_minus_count ?>">
					<?=$this->res_votes ?>
				</div>

				<? if($this->vote_control_enabled) { ?>
					<div class="unselectable">
						<a href="/vote/up/<?=$this->res_id ?>/" class="SendVote" data-res_id="<?=$this->res_id ?>" data-vote="up">[&plus;]</a>
					</div>
					<div class="unselectable">
						<a href="/vote/down/<?=$this->res_id ?>/" class="SendVote" data-res_id="<?=$this->res_id ?>" data-vote="down">[&ndash;]</a>
					</div>
				<? } ?>

				<? if($this->profile_link_enabled) { ?>
					<div class="unselectable">
						<a href="/user/profile/<?=$this->l_hash ?>/" class="ProfilePopup" data-hash="<?=$this->l_hash ?>">[Profils]</a>
					</div>
				<? } ?>

				<div class="unselectable">
					<a href="<?=$this->res_route ?>">[#]</a>
				</div>
			</div>
		</div>
		<div style="text-align: center;"><a href="/gallery/view/<?=$gd_nextid ?>/#pic-holder"><img src="<?=$image_path ?>" alt="Nākamā" width="500"></a></div>
		<div style="text-align: center;"><?=$image->res_data_compiled ?></div>

		<div class="TD-cat">Komentāri</div>
		<div class="TD-content"><? $this->CommentListT->print() ?></div>

		<div class="TD-cat">Pievienot komentāru</div>
		<div class="TD-content"><? $this->CommentFormT->print() ?></div>
		<?
	}
}
