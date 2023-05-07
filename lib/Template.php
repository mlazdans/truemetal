<?php

class Template extends TemplateBlock
{
	function __construct($file_name){
		if(!file_exists($file_name)){
			$this->error("file not found ($file_name)", E_USER_ERROR);
		}

		parent::__construct(NULL, $file_name, file_get_contents($file_name));
	}
}
