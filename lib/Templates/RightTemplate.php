<?php declare(strict_types = 1);

class RightTemplate extends Template
{

	/** @var RightItemTemplate[] $items */
	public $items = [];

	function add_item(RightItemTemplate $item): void
	{
		$this->items[] = $item;
	}

	protected function out(): void
	{
		foreach($this->items as $item) { ?>
			<div class="TD-cat"><?=$item->name ?></div>
			<div class="TD-content"><? $item->print() ?></div>
			<div class="List-sep"></div><?
		}
	}
}
