<?

class SpiderArticleComment implements SpiderResource
{
	private $q;
	private $db;
	private $sys_http_root;
	private $sys_lang;
	private $bad_users;

	function __construct($params)
	{
		require_once('../classes/class.Article.php');

		$this->db = $params['db'];
		$this->sys_http_root = $params['sys_http_root'];
		$this->sys_lang = $params['sys_lang'];
		$this->bad_users = bad_user_ids();
	} // __construct

	public function query($params = array())
	{
		$sql_add = '';
		if(!empty($params['ac_id']))
		{
			$sql_add .= " AND ac.ac_id = $params[ac_id]";
		}

		# load latest coments
		if(isset($params['ac_hash_date']))
		{
			$sql_add .= " AND ac.ac_hash_date > '$params[ac_hash_date]'";
		}

		$sql = sprintf("
SELECT
	ac.ac_id,
	ac.ac_userid,
	ac.ac_artid,
	ac.ac_hash,
	ac.ac_username,
	ac.ac_datacompiled,
	ac.ac_hash_date,

	a.art_name
FROM
	article_comments_$this->sys_lang ac
JOIN article_$this->sys_lang a ON a.art_id = ac.ac_artid
WHERE 
	ac.ac_visible = '%s' AND
	a.art_active = '%s' AND
	a.art_visible = '%s'
	$sql_add
", COMMENT_VISIBLE, ARTICLE_ACTIVE, ARTICLE_VISIBLE);

		$this->q = $this->db->Query($sql);
	} // query

	public function queryNew($latest)
	{
		$params = array();
		if(isset($latest['comment']))
		{
			$params['ac_hash_date'] = $latest['comment'];
		}

		return $this->query($params);
	} // queryNew

	public function fetch()
	{
		if($item = $this->db->FetchAssoc($this->q))
		{
			$item['ac_datacompiled'] = spider_process_search_data($item['ac_datacompiled']);

			return array(
				'url'=>"$this->sys_http_root/article/$item[ac_artid]/#comment$item[ac_id]",
				'hash'=>$item['ac_hash'],
				'data'=>strip_tags($item['ac_datacompiled']),
				'name'=>"$item[art_name]: $item[ac_username]",
				'date'=>$item['ac_hash_date'],
				'module_id'=>'comment',
				'module_name'=>'Komentāri',
				'bad_user'=>(int)in_array($item['ac_userid'], $this->bad_users),
			);
		}

		return false;
	} // fetch

	public function navigate($url)
	{
		$url = preg_replace("/^$this->sys_http_root/", "", $url);
		$parts = split('/', $url);
		if((count($parts) == 4) && ($parts[1] == 'article') && (preg_match('/^#comment(\d+)$/', $parts[3], $m)))
		{
			$ac_id = $m[1];
		} else {
			return false;
		}

		$this->query(array('ac_id'=>$ac_id));

		return $this->fetch();
	} // navigate

} // class SpiderArticleComment

?>
