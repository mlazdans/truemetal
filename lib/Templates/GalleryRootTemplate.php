<?php declare(strict_types = 1);

class GalleryRootTemplate extends AbstractTemplate
{
	public ?ViewResGalleryCollection $data = null;

	protected function out(): void
	{
		$data2 = array();
		foreach($this->data as $gal) {
			$k = empty($gal->gal_ggid) ? "e-".$gal->gal_id : $gal->gal_ggid;
			$data2[$k][] = $gal;
		}

		if($data2)
		{
			foreach($data2 as $items)
			{ ?>
				<div style="margin-bottom: 20px;"><?
				# NOTE: sooo stupid. Mus Get rid off!!
				if($items[0]->gal_ggid) { ?>
					<div id="gg_<?=$items[0]->gg_id ?>" class="TD-cat">
						<?=$items[0]->gg_name ?>
					</div><?
				} else { ?>
					<div id="gg_<?=$items[0]->gal_id ?>" class="TD-cat">
						<?=$items[0]->res_name ?>
					</div><?
				}
				?>
				<div style="padding-left: 20px;"><?=$items[0]->gg_data ?></div><?
				foreach($items as $gal) { ?>
					<div id="gal<?=$gal->gal_id ?>" style="padding-left: 20px;">
						<a href="/gallery/<?=$gal->gal_id ?>/"><?=specialchars($gal->res_name) ?></a> <?=$gal->res_data ?>
					</div><?
				} ?>
				</div><?
			}
		} else { ?>
			<div class="Info">
				Diemžēl pagaidām šeit nav nevienas galerijas.
			</div><?
		}
	}
}
