<?php declare(strict_types = 1);

class ErrorTemplate extends AbstractTemplate
{
	public bool $enabled     = false;
	public string|array $msg = '';

	protected function out(): void { ?>
		<div class="TD-cat">Kļūda:</div>
		<div class="Info error-form"><?=(is_array($this->msg) ? join("<br>", $this->msg) : $this->msg) ?></div><?
	}
}
