<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait ViewResGdDataEntityTrait
{
	function get_table_name(): string {
		return 'view_res_gd_data';
	}

	function get_pk(): string|array|null {
		return 'res_id';
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?ViewResGdDataType {
		return (new static)->get_single((new ViewResGdDataFilter(res_id: $ID))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): ViewResGdDataCollection {
		$col = new ViewResGdDataCollection;
		if($this->query($filters)){
			while($r = $this->fetch()){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch(): ?ViewResGdDataType {
		if($data = parent::fetch($this->Q)){
			return ViewResGdDataType::initFrom($data);
		} else {
			return null;
		}
	}

	function save(array|object $DATA): mixed {
		return parent::save($DATA);
	}

	function insert(array|object $DATA): mixed {
		return parent::insert($DATA);
	}

	function update(int|string|array $ID, array|object $DATA): bool {
		return parent::update($ID, $DATA);
	}
}
