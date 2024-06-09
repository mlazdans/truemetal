<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\DBA\AbstractFilter;

trait ViewResGalleryEntityTrait
{
	function get_table_name(): string {
		return 'view_res_gallery';
	}

	function get_pk(): string|array|null {
		return 'gal_id';
	}

	function get_gen(): ?string {
		return null;
	}

	static function get(int $ID, ?AbstractFilter $DF = null): ?ViewResGalleryType {
		return (new static)->get_single((new ViewResGalleryFilter(gal_id: $ID))->merge($DF));
	}

	function get_all(?AbstractFilter $filters = null): ViewResGalleryCollection {
		$col = new ViewResGalleryCollection;
		if($this->query($filters)){
			while($r = $this->fetch()){
				$col[] = $r;
			}
		}

		return $col;
	}

	function fetch(): ?ViewResGalleryType {
		if($data = parent::fetch($this->Q)){
			return ViewResGalleryType::initFrom($data);
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
