<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait ViewResGdEntityTrait
{
	function get_table_name(): string {
		return 'view_res_gd';
	}

	function get_pk(): string|array|null {
		return 'gd_id';
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?ViewResGdType {
		return (new static)->get_single((new ViewResGdFilter(gd_id: $ID))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): ViewResGdCollection {
		$col = new ViewResGdCollection;
		if($this->query($filters)){
			while($r = $this->fetch()){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch(): ?ViewResGdType {
		if($data = parent::fetch($this->Q)){
			return ViewResGdType::initFrom($data);
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
