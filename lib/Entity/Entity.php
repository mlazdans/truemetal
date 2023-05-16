<?php declare(strict_types = 1);

abstract class Entity extends \dqdp\DBA\AbstractEntity
{
	function __construct(){
		$this->set_trans(DB::getDB());

		parent::__construct();
	}
}
