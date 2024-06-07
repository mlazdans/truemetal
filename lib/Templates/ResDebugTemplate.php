<?php declare(strict_types = 1);

class ResDebugTemplate extends AbstractTemplate
{
	var object $res;
	protected function out(): void
	{
		printr($this->res);
	}
}
