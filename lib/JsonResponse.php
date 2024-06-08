<?php declare(strict_types = 1);

class JsonResponse implements TrueResponseInterface
{
	function __construct(private mixed $data) {
	}

	function print()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		print json_encode($this->data);
	}
}
