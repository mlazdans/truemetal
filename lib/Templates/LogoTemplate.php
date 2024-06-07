<?php declare(strict_types = 1);

class LogoTemplate extends AbstractTemplate
{
	protected function out(): void
	{ ?>
		<div class="TD-cat">Truemetal.lv logo</div>

		<div class="TD-content">
			<div class="List-item">
				<a href="/img/truemetal.lv-top-logo.svg">truemetal.lv logo (SVG)</a>
			</div>
			<!-- <div class="List-item">
				<a href="/img/truemetal_logo.png">truemetal.lv logo (PNG)</a>
			</div>
			<div class="List-item">
				<a href="/img/truemetal_logo-inkscape.svg">truemetal.lv logo (SVG Inkscape)</a>
			</div>
			<div class="List-item">
				<a href="/img/truemetal_logo-plain.svg">truemetal.lv logo (SVG Plain)</a>
			</div> -->
		</div><?
	}
}
