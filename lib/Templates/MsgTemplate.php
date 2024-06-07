<?php declare(strict_types = 1);

class MsgTemplate extends AbstractTemplate
{
	public bool $enabled = false;
	public string|array $msg = '';

	protected function out(): void { ?>
		<div class="Info"><?=(is_array($this->msg) ? join("<br>", $this->msg) : $this->msg) ?></div><?
	}
}
