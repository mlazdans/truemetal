<?php declare(strict_types = 1);

interface ResourceTypeInterface
{
	function Route(?int $c_id = null): string;
}
