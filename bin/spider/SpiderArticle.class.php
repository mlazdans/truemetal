<?

class SpiderArticle implements SpiderResource
{
	private $q;
	private $db;
	private $sys_http_root;
	private $sys_lang;
	private $modules = array();

	function __construct($params)
	{
		require_once('../classes/class.Article.php');

		$this->db = $params['db'];
		$this->sys_http_root = $params['sys_http_root'];
		$this->sys_lang = $params['sys_lang'];
		$this->modules = $this->getModules();
	} // __construct

	public function query($params = array())
	{
		$sql_add = '';
		if(!empty($params['art_id']))
		{
			$sql_add .= " AND a.art_id = $params[art_id]";
		}

		$sql = sprintf("
SELECT
	a.art_id,
	a.art_data,
	a.art_name,
	m.module_name,
	m.module_id,
	a.art_hash,
	a.art_hash_date
FROM
	article_$this->sys_lang a
JOIN modules_$this->sys_lang m ON m.mod_id = a.art_modid
WHERE
	m.module_active = '%s' AND
	m.module_visible = '%s' AND
	a.art_active = '%s' AND
	a.art_visible = '%s'
	$sql_add
", MOD_ACTIVE, MOD_VISIBLE, ARTICLE_ACTIVE, ARTICLE_VISIBLE);

		$sql_union = '';
		# load latest articles
		foreach($this->modules as $mod)
		{
			if(isset($params['art_hash_date_'.$mod]))
			{
				$date = $params['art_hash_date_'.$mod];
				$sql_union .= "($sql AND m.module_id = '$mod' AND a.art_hash_date > '$date') UNION ";
			}
		}
		if($sql_union = substr($sql_union, 0, -7)) // remove last union
		{
			$sql = $sql_union;
		}

		$this->q = $this->db->Query($sql);
	} // query

	public function queryNew($latest)
	{
		$params = array();
		foreach($this->modules as $mod)
		{
			if(isset($latest[$mod]))
			{
				$params['art_hash_date_'.$mod] = $latest[$mod];
			}
		}

		return $this->query($params);
	} // queryNew

	public function fetch()
	{
		//return $this->db->FetchAssoc($this->q);
		if($item = $this->db->FetchAssoc($this->q))
		{
			$item['art_data'] =
				spider_process_search_data($item['art_name']).
				' '.
				spider_process_search_data($item['art_data']);

			return array(
				'url'=>"$this->sys_http_root/article/$item[art_id]/",
				'hash'=>$item['art_hash'],
				'data'=>strip_tags($item['art_data']),
				'name'=>$item['art_name'],
				'date'=>$item['art_hash_date'],
				'module_id'=>$item['module_id'],
				'module_name'=>$item['module_name'],
				'bad_user'=>0,
			);
		}

		return false;
	} // fetch

	public function navigate($url)
	{
		$url = preg_replace("/^$this->sys_http_root/", "", $url);
		$parts = split('/', $url);

		if((count($parts) == 4) && ($parts[1] == 'article') && ($parts[3] == ''))
		{
			$art_id = $parts[2];
		} else {
			return false;
		}

		$this->query(array('art_id'=>$art_id));

		return $this->fetch();
	} // navigate

	private function getModules()
	{
		$sql = sprintf("
SELECT
	m.module_id
FROM
	article_$this->sys_lang a
JOIN modules_$this->sys_lang m ON m.mod_id = a.art_modid
WHERE
	m.module_active = '%s' AND
	m.module_visible = '%s' AND
	a.art_active = '%s' AND
	a.art_visible = '%s'
GROUP BY
	a.art_modid
", MOD_ACTIVE, MOD_VISIBLE, ARTICLE_ACTIVE, ARTICLE_VISIBLE);

		if($data = $this->db->Execute($sql))
		{
			foreach($data as $item)
			{
				$modules[] = $item['module_id'];
			}
		}

		return $modules;
	} // getModules

} // class SpiderArticle

?>
