<?php declare(strict_types = 1);

class SearchLogTemplate extends AbstractTemplate
{
	public SearchLogCollection $data;

	protected function out(): void
	{
		?>
		<div class="TD-cat">Ko mēs meklējam?</div>

		<div class="TD-content">
			<? foreach($this->data as $item) { ?>
				<div class="List-item">
					<a href="/search/?search_q=<?=urlencode($item->sl_q) ?>" title=""><?=specialchars($item->sl_q )?></a>
				</div>
			<? } ?>
		</div><?
	}
}
