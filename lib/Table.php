<?php declare(strict_types = 1);

class Table
{
	const ARTICLE = 1;
	const FORUM = 2;
	const COMMENT = 3;
	const GALLERY = 4;
	const GALLERY_DATA = 5;

	static function getName($id): string
	{
		if($id == Table::COMMENT){
			return 'comment';
		}
		if($id == Table::ARTICLE){
			return 'article';
		}
		if($id == Table::FORUM){
			return 'forum';
		}
		if($id == Table::GALLERY){
			return 'gallery';
		}
		if($id == Table::GALLERY_DATA){
			return 'gallery_data';
		}

		throw new InvalidArgumentException("Invalid table ID: $id");
	}
}
