<?php declare(strict_types = 1);

class RightTemplate extends AbstractTemplate
{
	/** @var RightItemAbstractTemplate[] $items */
	protected $items = [];

	function addItem(RightItemAbstractTemplate $item): void
	{
		$this->items[] = $item;
	}

	protected function out(): void
	{
		foreach($this->items as $item) { ?>
			<div class="TD-cat"><?=$item->name ?></div>
			<div class="TD-content"><? $item->print() ?></div>
			<div class="List-sep"></div><?
		} ?>
		<div class="TD-cat">Viskas</div>
		<div class="TD-content">
			<div class="List-item">
				<a href="/archive/">Arhīvs</a>
			</div>
			<div class="List-item">
				<a href="/logo/">Truemetal.lv logo</a>
			</div>
		</div><?
	}
}
