<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait AttendEntityTrait
{
	function get_table_name(): string {
		return 'attend';
	}

	function get_pk(): string|array|null {
		return ['l_id','res_id'];
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(array $ID, ?AbstractFilter $DF = null): ?AttendType {
		return (new static)->get_single((new AttendFilter(l_id: $ID['l_id'], res_id: $ID['res_id']))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): AttendCollection {
		$col = new AttendCollection;
		if($this->query($filters)){
			while($r = $this->fetch()){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch(): ?AttendType {
		if($data = parent::fetch($this->Q)){
			return AttendType::initFrom($data);
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
