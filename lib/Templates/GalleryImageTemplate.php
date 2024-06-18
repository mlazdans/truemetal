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

		<? $this->profile() ?>

		<div style="text-align: center;"><a href="/gallery/view/<?=$gd_nextid ?>/#pic-holder"><img src="<?=$image_path ?>" alt="Nākamā" width="500"></a></div>
		<div style="text-align: center;"><?=$image->res_data_compiled ?></div>

		<div class="TD-cat">Komentāri</div>
		<div class="TD-content"><? $this->CommentListT->print() ?></div>

		<div class="TD-content"><? $this->CommentFormT->print() ?></div>
		<?
	}
}
