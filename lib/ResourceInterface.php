<?php declare(strict_types = 1);

interface ResourceInterface
{
	static function RouteFromRes(array $res, int $c_id = 0): string;
}
