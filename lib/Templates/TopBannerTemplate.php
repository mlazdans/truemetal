<?php declare(strict_types = 1);

class TopBannerTemplate extends AbstractTemplate
{
	var string $banner_href   = '';
	var string $banner_img    = '';
	var string $banner_height = '';
	var string $banner_alt    = '';

	protected function out(): void
	{ ?>
		<a href="<?=$this->banner_href ?>"><img src="/img/<?=$this->banner_img ?>" width="<?=$this->banner_img ?>" height="<?=$this->banner_height ?>" alt="<?=$this->banner_alt ?>"></a>
	<?
	}
}
