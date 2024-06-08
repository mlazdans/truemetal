<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait LoginAcceptEntityTrait
{
	function get_table_name(): string {
		return 'login_accept';
	}

	function get_pk(): string|array|null {
		return 'la_id';
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?LoginAcceptType {
		return (new static)->get_single((new LoginAcceptFilter(la_id: $ID))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): LoginAcceptCollection {
		$col = new LoginAcceptCollection;
		if($this->query($filters)){
			while($r = $this->fetch()){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch(): ?LoginAcceptType {
		if($data = parent::fetch($this->Q)){
			return LoginAcceptType::initFrom($data);
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
