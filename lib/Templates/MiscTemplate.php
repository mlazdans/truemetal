<?php declare(strict_types = 1);

class MiscTemplate extends RightItemTemplate
{
	public string $name = "Viskas";

	protected function out(): void
	{ ?>
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
