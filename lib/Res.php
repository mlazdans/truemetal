<?php declare(strict_types = 1);

class Res
{
	const TYPE_STD = 0;
	const TYPE_EVENT = 1;

	const STATE_ACTIVE = 'Y';
	const STATE_INACTIVE = 'N';
	const STATE_VISIBLE = 'Y';
	const STATE_INVISIBLE = 'N';
	const STATE_ALL = false;

	const ACT_VALIDATE = true;
	const ACT_DONTVALIDATE = false;

	var $types = array(
		Res::TYPE_STD=>'Forums',
		Res::TYPE_EVENT=>'Pasākums',
		);

	protected $table_id;
	protected $login_id;

	function Get(Array $params = array())
	{
		$sql = "SELECT * FROM `res`";

		$sql_add = array();

		if(!empty($params['res_id']))
			$sql_add[] = sprintf("res_id = %d", $params['res_id']);

		if($sql_add)
			$sql .= " WHERE ".join(' AND ', $sql_add);

		if(!empty($params['order']))
			$sql .= " ORDER BY $params[order] ";

		if(!empty($params['limit']))
			$sql .= " LIMIT ".$params['limit'];

		return (empty($params['res_id']) ? DB::Execute($sql) : DB::ExecuteSingle($sql));
	}

	function Add()
	{
		$sql = sprintf(
			"INSERT INTO `res` (`table_id`, `login_id`, `res_entered`) VALUES (%s, %s, CURRENT_TIMESTAMP);",
			($this->table_id ? $this->table_id : "NULL"),
			($this->login_id ? $this->login_id : "NULL"),
		);

		return (DB::Execute($sql) ? DB::LastID() : false);
	}

	# TODO: katrā klasē atsevišķi
	function GetAllData($res_id)
	{
		$res_data = $this->Get(array(
			'res_id'=>$res_id,
			));

		if(!$res_data) {
			return false;
		}

		switch($res_data['table_id'])
		{
			case Table::ARTICLE:
				$D = new Article();
				return array_merge($res_data, $D->load(array(
					'res_id'=>$res_data['res_id'],
					)));
			case Table::FORUM:
				$D = new Forum();
				return array_merge($res_data, $D->load(array(
					'res_id'=>$res_data['res_id'],
					)));
				break;
			case Table::COMMENT:
				$D = new Comment();
				return array_merge($res_data, $D->Get(array(
					'res_id'=>$res_data['res_id'],
					)));
				break;
			case Table::GALLERY:
				$D = new Gallery();
				return array_merge($res_data, $D->load(array(
					'res_id'=>$res_data['res_id'],
					)));
				break;
			case Table::GALLERY_DATA:
				$D = new GalleryData();
				return array_merge($res_data, $D->load(array(
					'res_id'=>$res_data['res_id'],
					)));
				break;
		}

		return false;
	}

	# TODO: katrā klasē atsevišķi
	public static function Route($res_id, $c_id = 0)
	{
		$location = "/";

		$Res = new Res();
		if(!($resource = $Res->GetAllData($res_id))){
			return $location;
		}

		switch($resource['table_id'])
		{
			case Table::ARTICLE:
				$location = Article::Route($resource, $c_id);;
				break;
			case Table::FORUM:
				$location = Forum::Route($resource, $c_id);
				break;
			case Table::COMMENT:
				$RC = new ResComment;
				$C = $RC->get(array(
					'c_id'=>$resource['c_id'],
					));
				$location = Res::Route($C['parent_res_id'], $resource['c_id']);
				break;
			case Table::GALLERY:
				$location = Gallery::Route($resource, $c_id);
				break;
			case Table::GALLERY_DATA:
				$location = GalleryData::Route($resource, $c_id);
				break;
		}

		return $location;
	}

	public static function hasNewComments($item): bool
	{
		if(!User::logged() || empty($item['res_id'])){
			return false;
		}

		$res_id = $item['res_id'];

		if(isset($_SESSION['res']['viewed_date'][$res_id]) && $item['res_comment_lastdate'])
			return (strtotime($item['res_comment_lastdate']) > strtotime($_SESSION['res']['viewed_date'][$res_id]));

		if(isset($_SESSION['res']['viewed'][$res_id]))
			return ($item['res_comment_count'] > $_SESSION['res']['viewed'][$res_id]);

		if(isset($_SESSION['res']['viewed_before']) && $item['res_comment_lastdate'])
			return ($_SESSION['res']['viewed_before'] < strtotime($item['res_comment_lastdate']));

		return ($item['res_comment_count'] > 0);
	}

	public static function markCommentCount($item): void
	{
		if(User::logged()){
			$_SESSION['res']['viewed_date'][$item['res_id']] = $item['res_comment_lastdate'];
		}
	}
}
