<?php declare(strict_types = 1);

class JsonResponse implements TrueResponseInterface
{
	function __construct(private mixed $data)
	{
	}

	function out()
	{
		header('Content-Type: text/javascript; charset=utf-8');
		print json_encode($this->data);
	}
}
