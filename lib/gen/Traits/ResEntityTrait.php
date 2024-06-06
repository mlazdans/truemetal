<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait ResEntityTrait
{
	function get_table_name(): string {
		return 'res';
	}

	function get_pk(): string|array|null {
		return 'res_id';
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?ResType {
		return (new static)->get_single((new ResFilter(res_id: $ID))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): ResCollection {
		$col = new ResCollection;
		if($q = $this->query($filters)){
			while($r = $this->fetch($q)){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch($q): ?ResType {
		if($data = parent::fetch($q)){
			return ResType::initFrom($data);
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
