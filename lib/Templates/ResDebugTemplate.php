<?php declare(strict_types = 1);

class ResDebugTemplate extends Template
{
	var object $res;
	protected function out(): void
	{
		printr($this->res);
	}
}
