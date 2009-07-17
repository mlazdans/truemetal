<?php

class SpiderForum implements SpiderResource
{
	private $q;
	private $db;
	private $sys_http_root;
	private $bad_users;

	function __construct($params)
	{
		require_once('../classes/class.Forum.php');

		$this->db = $params['db'];
		$this->sys_http_root = $params['sys_http_root'];
		$this->bad_users = bad_user_ids();
	} // __construct

	public function query($params = array())
	{
		$sql_add = '';
		if(!empty($params['forum_id']))
		{
			$sql_add .= " AND f.forum_id = $params[forum_id]";
		}

		# load latest forum items
		if(isset($params['forum_hash_date']))
		{
			$sql_add .= " AND f.forum_hash_date > '$params[forum_hash_date]'";
		}

		$sql = sprintf("
SELECT
	f.forum_id,
	f.forum_forumid,
	f.forum_datacompiled,
	f.forum_hash,
	f.forum_hash_date,
	f.forum_username,
	f.forum_userid,

	f2.forum_name forum_theme,
	f2.forum_userid forum_themeuserid
FROM
	forum f
JOIN forum f2 ON f2.forum_id = f.forum_forumid
WHERE
	f.forum_active = '%s' AND
	f2.forum_active = '%s' AND
	f2.forum_allowchilds = '%s'
	$sql_add
", FORUM_ACTIVE, FORUM_ACTIVE, FORUM_PROHIBITCHILDS);

		$this->q = $this->db->Query($sql);
	} // query

	public function queryNew($latest)
	{
		$params = array();
		if(isset($latest['forum']))
		{
			$params['forum_hash_date'] = $latest['forum'];
		}

		return $this->query($params);
	} // queryNew

	public function fetch()
	{
		if($item = $this->db->FetchAssoc($this->q))
		{
			$item['forum_datacompiled'] =
				spider_process_search_data($item['forum_theme']).
				' '.
				spider_process_search_data($item['forum_datacompiled']);

			return array(
				'url'=>"$this->sys_http_root/forum/$item[forum_forumid]/#comment$item[forum_id]",
				'hash'=>$item['forum_hash'],
				'data'=>strip_tags($item['forum_datacompiled']),
				'name'=>"$item[forum_theme]: $item[forum_username]",
				'date'=>$item['forum_hash_date'],
				'module_id'=>'forum',
				'module_name'=>'Forums',
				'bad_user'=>(int)(in_array($item['forum_userid'], $this->bad_users) || in_array($item['forum_themeuserid'], $this->bad_users)),
			);
		}

		return false;
	} // fetch

	public function navigate($url)
	{
		$url = preg_replace("/^$this->sys_http_root/", "", $url);
		$parts = split('/', $url);
		if((count($parts) == 4) && ($parts[1] == 'forum') && (preg_match('/^#comment(\d+)$/', $parts[3], $m)))
		{
			$forum_id = $m[1];
		} else {
			return false;
		}

		$this->query(array('forum_id'=>$forum_id));

		return $this->fetch();
	} // navigate

} // class SpiderForum

