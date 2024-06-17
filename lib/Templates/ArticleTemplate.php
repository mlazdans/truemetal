<?php declare(strict_types = 1);

class ArticleTemplate extends AbstractResTemplate
{
	public ?int $art_id = null;
	public ?string $hl = null;

	protected function out(): void
	{ ?>
		<div class="TD-cat">
			<div class="res-name"><a class="caption" href="<?=$this->res_route ?>"><?=$this->res_name ?></a></div>
		</div>

		<div class="TD-content">

		<? $this->profile() ?>

		<div class="Article-item">
			<div class="data">
				<div class="intro">
				<?=$this->res_intro ?>
				</div>
				<?=$this->res_data ?>
			</div>
		</div>
		</div>

		<div class="TD-cat" id="art-comments-<?=$this->art_id ?>">Komentāri</div>
		<div class="TD-content"><? $this->CommentListT->print() ?></div>

		<div class="TD-cat">Pievienot komentāru</div>
		<div class="TD-content"><? $this->CommentFormT->print() ?></div><?
	}
}
