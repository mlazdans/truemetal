<?php declare(strict_types = 1);

use dqdp\SQL\Select;
use dqdp\TODO;

class Res
{
	const TYPE_STD = 0;
	const TYPE_EVENT = 1;

	// const STATE_ACTIVE = 'Y';
	// const STATE_INACTIVE = 'N';
	// const STATE_VISIBLE = 'Y';
	// const STATE_INVISIBLE = 'N';
	// const STATE_ALL = false;

	// const ACT_VALIDATE = true;
	// const ACT_DONTVALIDATE = false;

	var array $types = [
		Res::TYPE_STD=>'Forums',
		Res::TYPE_EVENT=>'Pasākums',
	];

	// protected $table_id;
	// protected $login_id;

	static function load(array $params)
	{
		$sql = (new Select('res.*, res_meta.*'))
		->Join('res_meta', 'res_meta.res_id = res.res_id')
		->From('res')
		;

		join_logins($sql);

		if(!empty($params['res_id'])){
			$sql->Where(["res.res_id = ?", $params['res_id']]);
		}

		if(defaulted($params, 'res_visible'))
		{
			$sql->Where("res.res_visible = 1");
		} elseif(!ignored($params, 'res_visible')){
			$sql->Where(["res.res_visible = ?", $params['res_visible']]);
		}

		if(empty($params['order'])){
			$sql->OrderBy("res.res_entered DESC");
		} else {
			$sql->OrderBy($params['order']);
		}

		if(isset($params['rows']))
		{
			$sql->Rows((int)$params['rows']);
		}

		if(isset($params['limit']))
		{
			new TODO("Nodalīt rows un offset");
		}

		return (empty($params['res_id']) ? DB::Execute($sql) : DB::ExecuteSingle($sql));
	}

	// function Add()
	// {
	// 	$sql = sprintf(
	// 		"INSERT INTO `res` (`table_id`, `login_id`, `res_entered`) VALUES (%s, %s, CURRENT_TIMESTAMP);",
	// 		($this->table_id ? $this->table_id : "NULL"),
	// 		($this->login_id ? $this->login_id : "NULL"),
	// 	);

	// 	return (DB::Execute($sql) ? DB::LastID() : false);
	// }

	static function GetAll(int $res_id)
	{
		$res_data = static::load(['res_id'=>$res_id]);

		if(!$res_data) {
			return false;
		}

		switch($res_data['table_id'])
		{
			case Table::ARTICLE:
				return Article::load(['res_id'=>$res_id]);
			case Table::FORUM:
				return Forum::load(['res_id'=>$res_id]);
				// $D = new Forum();
				// return array_merge($res_data, $D->load(array(
				// 	'res_id'=>$res_data['res_id'],
				// 	)));
			case Table::COMMENT:
				return Comment::load(['res_id'=>$res_id]);
				// $D = new Comment();
				// return array_merge($res_data, $D->Get(array(
				// 	'res_id'=>$res_data['res_id'],
				// 	)));
				// break;
			case Table::GALLERY:
				new TODO("Get Gallery");
				// $D = new Gallery();
				// return array_merge($res_data, $D->load(array(
				// 	'res_id'=>$res_data['res_id'],
				// 	)));
				// break;
			case Table::GALLERY_DATA:
				new TODO("Get GalleryData");
				// $D = new GalleryData();
				// return array_merge($res_data, $D->load(array(
				// 	'res_id'=>$res_data['res_id'],
				// 	)));
				// break;
		}

		throw new InvalidArgumentException("Table unknown: $res_data[table_id]");
	}

	# TODO: katrā klasē atsevišķi
	static function Route(int $res_id, int $c_id = 0): string
	{
		if(!($res = Res::GetAll($res_id))){
			return "/";
		}

		return static::RouteFromRes($res, $c_id);
	}

	static function RouteFromRes(array $res, int $c_id = 0): string
	{
		# TODO: visās uz res balstītās tabulās primary key pārsaukt par doc_id
		switch($res['table_id'])
		{
			case Table::ARTICLE:
				return Article::RouteFromRes($res, $c_id);
			case Table::FORUM:
				return Forum::RouteFromRes($res, $c_id);
			case Table::COMMENT:
				return Comment::RouteFromRes($res, $c_id);
			// case Table::GALLERY:
			// 	return Gallery::RouteFromRes($res, $c_id);
			// case Table::GALLERY_DATA:
			// 	return GalleryData::RouteFromRes($res, $c_id);
		}

		throw new InvalidArgumentException("Table unknown: $res[table_id]");
	}

	static function hasNewComments($item): bool
	{
		if(!User::logged() || empty($item['res_id'])){
			return false;
		}

		$res_id = $item['res_id'];

		if(isset($_SESSION['res']['viewed_date'][$res_id]) && $item['res_comment_last_date'])
			return (strtotime($item['res_comment_last_date']) > strtotime($_SESSION['res']['viewed_date'][$res_id]));

		if(isset($_SESSION['res']['viewed'][$res_id]))
			return ($item['res_comment_count'] > $_SESSION['res']['viewed'][$res_id]);

		if(isset($_SESSION['res']['viewed_before']) && $item['res_comment_last_date'])
			return ($_SESSION['res']['viewed_before'] < strtotime($item['res_comment_last_date']));

		return ($item['res_comment_count'] > 0);
	}

	static function markCommentCount($item): void
	{
		if(User::logged()){
			$_SESSION['res']['viewed_date'][$item['res_id']] = $item['res_comment_last_date'];
		}
	}
}
