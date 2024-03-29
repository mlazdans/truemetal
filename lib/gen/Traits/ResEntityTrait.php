<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait ResEntityTrait {
	function __construct(){
		parent::__construct();
	}

	function getTableName(): ?string {
		return 'res';
	}

	function getProcName(): ?string {
		return null;
	}

	function getPK(): string|array|null {
		return 'res_id';
	}

	function getGen(): ?string {
		return null;
	}

	function getProcArgs(): ?array {
		return null;
	}

	function getAll(?AbstractFilter $filters = null): ResCollection {
		$col = new ResCollection;
		if($q = $this->query($filters)){
			while($r = $this->fetch($q)){
				$col[] = $r;
			}
		}

		return $col;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?ResType {
		return (new static)->getSingle((new ResFilter(res_id: $ID))->merge($DF));
	}

	function fetch($q): ?ResType {
		// return ResType::fromDBObject(parent::fetch($q));
		if($data = parent::fetch($q)){
			return ResType::initFrom($data);
		} else {
			return null;
		}
	}

	// private function savePreprocessor(array|object $DATA, \Closure $f): mixed {
	// 	if($DATA instanceof ResType){
	// 		if(method_exists($this, "beforeSave")){
	// 			if($PROC_DATA = $this->beforeSave($DATA)){
	// 				return $f($PROC_DATA);
	// 			} else {
	// 				return null;
	// 			}
	// 		} else {
	// 			return $f($DATA);
	// 		}
	// 	} else {
	// 		throw new InvalidTypeException($DATA);
	// 	}
	// }

	function save(array|object $DATA): mixed {
		// return $this->savePreprocessor($DATA, function(array|object $DATA){
		// 	return parent::save(ResType::toDBObject($DATA));
		// });
		return parent::save($DATA);
	}

	function insert(array|object $DATA): mixed {
		// return $this->savePreprocessor($DATA, function(array|object $DATA){
		// 	return parent::insert(ResType::toDBObject($DATA));
		// });
		return parent::insert($DATA);
	}

	function update(int|string|array $ID, array|object $DATA): bool {
		return parent::update($ID, $DATA);

		// return $this->savePreprocessor($DATA, function(array|object $DATA) use ($ID) {
		// 	return parent::update($ID, ResType::toDBObject($DATA));
		// }) ?? false;
	}
}
