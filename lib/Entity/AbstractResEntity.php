<?php declare(strict_types = 1);

abstract class AbstractResEntity extends Entity
{
	function __construct(){
		parent::__construct();
	}

	// abstract function get_filter(): ResFilter;
	// abstract function load(): Collection;
	// abstract function load_single(): ?DataObject;
	// abstract function load_by_id(int $id): ?DataObject;
	abstract static function getById(int $id, bool $ignore_disabled = false);
	abstract static function getByResId(int $res_id, bool $ignore_disabled = false);

	// function load_by_res_id(int $res_id): ?DataObject
	// {
	// 	$this->get_filter()->res_id = $res_id;

	// 	return $this->load_single();
	// }

	# TODO: vajadzētu izdomāt, kā enforcēt, lai bugus var ātrāk izķert
	// protected function _load_single(): ?DataObject
	// {
	// 	$data = $this->load();

	// 	assert($data->count() <= 1);

	// 	if($data->count())
	// 	{
	// 		return $data[0];
	// 	}

	// 	return null;
	// }
}
