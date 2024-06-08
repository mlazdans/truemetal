<?php declare(strict_types = 1);

class ViewimageTemplate extends AbstractTemplate
{
	public ?string $pic_path = null;
	public ?string $l_nick = null;

	protected function out(): void
	{
		 if($this->pic_path) { ?>
			<div class="TD-cat">Bilde: <?=specialchars($this->l_nick) ?></div>
			<div class="Info" style="text-align: center;">
				<img src="<?=$this->pic_path ?>" alt="">
			</div><?
		}
	}
}
