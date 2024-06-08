<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait LoginsEntityTrait
{
	function get_table_name(): string {
		return 'logins';
	}

	function get_pk(): string|array|null {
		return 'l_id';
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?LoginsType {
		return (new static)->get_single((new LoginsFilter(l_id: $ID))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): LoginsCollection {
		$col = new LoginsCollection;
		if($this->query($filters)){
			while($r = $this->fetch()){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch(): ?LoginsType {
		if($data = parent::fetch($this->Q)){
			return LoginsType::initFrom($data);
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
