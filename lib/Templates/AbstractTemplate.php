<?php declare(strict_types = 1);

abstract class AbstractTemplate
{
	public bool $enabled = true;
	// private ?string $buffer = null;
	private ?string $out_function = null;

	function parse(): ?string
	{
		ob_start();
		$this->print();
		if(($buffer = ob_get_clean()) === false){
			return null;
		} else {
			return $buffer;
		}

		// if(is_null($this->buffer)){
		// 	ob_start();
		// 	$this->print();
		// 	if(($buffer = ob_get_clean()) === false){
		// 		# TODO: error?
		// 		$this->buffer = "";
		// 	} else {
		// 		$this->buffer = $buffer;
		// 	}
		// }

		// return $this->buffer;
	}

	function set_out(string $func): static
	{
		$this->out_function = $func;

		return $this;
	}

	function print(): static
	{
		if($this->enabled){
			if($this->out_function) {
				$this->{$this->out_function}();
			} else {
				$this->out();
			}
		}

		return $this;
	}

	function set_enabled(): static
	{
		$this->enabled = true;

		return $this;
	}

	function set_disabled(): static
	{
		$this->enabled = false;

		return $this;
	}

	protected abstract function out(): void;
}
