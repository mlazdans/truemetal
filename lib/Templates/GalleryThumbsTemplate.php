<?php declare(strict_types = 1);

class GalleryThumbsTemplate extends AbstractTemplate
{
	public ViewResGalleryType $gal;
	public ViewResGdCollection $thumbs;
	public string $gal_name;
	public bool $is_cache_enabled = true;

	protected function out(): void
	{
		if(!$this->thumbs->count()) {
			return;
		}

		$gal = $this->gal;

		if($gal->gal_ggid){
			$gal_jump_id = "gg_".$gal->gal_ggid;
		} else {
			$gal_jump_id = "gal_".$gal->gal_id;
		}

		$tpr = 5;
		$c = 0;

		$thumb_count = $this->thumbs->count();
		?>
		<div class="TD-cat">
			<a href="/gallery/#<?=$gal_jump_id ?>">Galerijas</a> / <?=specialchars($gal->res_name) ?>
		</div>

		<? foreach($this->thumbs as $thumb) {
			$c++;

			if($this->is_cache_enabled && ($hash = cache_hash($thumb->gd_id."thumb.jpg")) && cache_exists($hash)) {
				$thumb_path = cache_http_path($hash);
			} else {
				$thumb_path = "/gallery/thumb/$thumb->gd_id/";
			}

			$res_votes = format_vote($thumb->res_votes);
			$comment_class = GalleryData::has_new_comments($thumb) ? "Comment-count-new" : "Comment-count-old";

			if($c % $tpr == 1){ ?>
				<div style="text-align: center; margin-bottom: 1em;"><?
			} ?>

			<div class="unselectable" style="display: inline-block;position: relative;left:0;padding:0; margin: 0 2px;">
				<div class="List-item" style="text-align: left;">
					<div class="vote <?=comment_vote_class($thumb->res_votes) ?> vote-value" style="display: inline-block;text-align: center; padding:0; border:none;"><?=$res_votes ?></div>
					Kom. (<div class="<?=$comment_class ?>" style="display: inline-block;"><?=$thumb->res_comment_count ?></div>)
				</div>
				<a href="/gallery/view/<?=$thumb->gd_id ?>/#pic-holder"><img src="<?=$thumb_path ?>" alt="" class="img-thumb" width="120"></a>
			</div>

			<? if(($c % $tpr == 0) || ($c == $thumb_count)) { ?>
				</div><?
			}
		}
	}
}
