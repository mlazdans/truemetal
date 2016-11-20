<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

class Table
{
	const ARTICLE = 1;
	const FORUM = 2;
	const COMMENT = 3;
	const GALLERY = 4;

	static function getName($id)
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
		return false;
	}
} // class::Table

