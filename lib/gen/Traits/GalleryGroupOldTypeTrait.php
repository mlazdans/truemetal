<?php declare(strict_types = 1);

# AUTOGENERATED - DO NOT MODIFY!

use dqdp\PropertyInitTrait;
use dqdp\DBA\Types\Varchar;

trait GalleryGroupOldTypeTrait {
	use PropertyInitTrait;

	readonly int $gg_id;
	readonly string $gg_name;
	readonly string $gg_data;
	readonly string $gg_date;
	readonly string $gg_entered;

	function __construct(array|object|null $data = null, array|object|null $defaults = null, bool $is_dirty = false) {
		parent::__construct($data, $defaults, $is_dirty);
	}

	static function initgg_name(mixed $v): string {
		return (string)(new VarChar($v, 64));
	}

	function save(): mixed {
		return (new GalleryGroupOldEntity)->save($this);
	}

	function insert(): mixed {
		return (new GalleryGroupOldEntity)->insert($this);
	}

	function delete(): bool {
		return (new GalleryGroupOldEntity)->delete($this->gg_id);
	}

	function update(): bool {
		return (new GalleryGroupOldEntity)->update($this->gg_id, $this);
	}
}
