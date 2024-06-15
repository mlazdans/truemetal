<?php declare(strict_types = 1);

class TopBannerTemplate extends AbstractTemplate
{
	var string $banner_href = '';
	var string $banner_img  = '';
	var string $banner_alt  = '';

	protected function out(): void
	{ ?>
		<a
			href="<?=$this->banner_href ?>"
			style="display: block; width: 100%;height: 100%; background: url('/img/<?=$this->banner_img ?>') no-repeat; background-size: cover;"
			title="<?=specialchars($this->banner_alt) ?>"
			>
		</a>
	<?
	}
}
