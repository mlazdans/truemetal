<?php declare(strict_types = 1);

class SearchParams
{
	function __construct(
		public string $q,
		public string $index,
		public array $filters,
		public int $limit,
	) { }
}
