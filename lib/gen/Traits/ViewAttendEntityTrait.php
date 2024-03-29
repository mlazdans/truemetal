<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait ViewAttendEntityTrait {
	function __construct(){
		parent::__construct();
	}

	function getTableName(): ?string {
		return 'view_attend';
	}

	function getProcName(): ?string {
		return null;
	}

	function getPK(): string|array|null {
		return null;
	}

	function getGen(): ?string {
		return null;
	}

	function getProcArgs(): ?array {
		return null;
	}

	function getAll(?AbstractFilter $filters = null): ViewAttendCollection {
		$col = new ViewAttendCollection;
		if($q = $this->query($filters)){
			while($r = $this->fetch($q)){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch($q): ?ViewAttendType {
		// return ViewAttendType::fromDBObject(parent::fetch($q));
		if($data = parent::fetch($q)){
			return ViewAttendType::initFrom($data);
		} else {
			return null;
		}
	}

	// private function savePreprocessor(array|object $DATA, \Closure $f): mixed {
	// 	if($DATA instanceof ViewAttendType){
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
		// 	return parent::save(ViewAttendType::toDBObject($DATA));
		// });
		return parent::save($DATA);
	}

	function insert(array|object $DATA): mixed {
		// return $this->savePreprocessor($DATA, function(array|object $DATA){
		// 	return parent::insert(ViewAttendType::toDBObject($DATA));
		// });
		return parent::insert($DATA);
	}

	function update(int|string|array $ID, array|object $DATA): bool {
		return parent::update($ID, $DATA);

		// return $this->savePreprocessor($DATA, function(array|object $DATA) use ($ID) {
		// 	return parent::update($ID, ViewAttendType::toDBObject($DATA));
		// }) ?? false;
	}
}
