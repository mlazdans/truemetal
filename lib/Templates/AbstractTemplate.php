<?php declare(strict_types = 1);

abstract class AbstractTemplate
{
	public bool $enabled = true;
	private ?string $buffer = null;

	// function parse(): string
	// {
	// 	if(is_null($this->buffer)){
	// 		ob_start();
	// 		$this->print();
	// 		if(($buffer = ob_get_clean()) === false){
	// 			# TODO: error?
	// 			$this->buffer = "";
	// 		} else {
	// 			$this->buffer = $buffer;
	// 		}
	// 	}

	// 	return $this->buffer;
	// }

	function print()
	{
		if($this->enabled){
			$this->out();
		}

		return $this;
	}

	function set_enabled()
	{
		$this->enabled = true;

		return $this;
	}

	function set_disabled()
	{
		$this->enabled = false;

		return $this;
	}

	protected abstract function out(): void;
}
