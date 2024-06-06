<?php declare(strict_types = 1);

abstract class AbstractTemplate
{
	public bool $enabled = true;
	protected ?string $buffer = null;

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

	function set_disabled(): self
	{
		$this->enabled = false;

		return $this;
	}

	abstract protected function out(): void;
}
