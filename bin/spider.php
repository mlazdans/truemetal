<?
/*

SQL_NO_CACHE

* URL skaits
	UPDATE spider_words sw SET sw.sw_urls = (SELECT COUNT(*) FROM spider_words2urls w2u WHERE w2u.sw_id = sw.sw_id)
		Query OK, 229802 rows affected (2 min 6.92 sec)
		Rows matched: 229802  Changed: 229802  Warnings: 0

* Vārdu statistika
	SELECT *
	FROM `spider_words`
	ORDER BY `spider_words`.`sw_urls` DESC

	SLOW++
	------
	SELECT sw.sw_word, w2u.word_count FROM `spider_words2urls` w2u
	JOIN spider_words sw ON sw.sw_id = w2u.sw_id
	GROUP BY sw.sw_id
	ORDER BY word_count desc
*/

// TODO: tabulai sw_words automātiski apdeitot URL skaitu (sw_urls)
// DONE: crawleris pēc datuma

$i_am_admin = true;
require_once('../includes/inc.config.php');
require_once('../includes/inc.dbconnect.php');
require_once('../includes/inc.utils.php');

require_once('spider/SpiderResource.interface.php');
require_once('spider/SpiderForum.class.php');
require_once('spider/SpiderArticle.class.php');
require_once('spider/SpiderArticleComment.class.php');

$SPIDER_WORD_LEN_MAX = 40;

mb_regex_encoding($sys_encoding);
mb_internal_encoding($sys_encoding);

$sys_lang = $sys_default_lang;

function spider_process_search_data($data)
{
	$patt = array(
		'/&[^;]*;/imsU',
		'/</imsU',
		'/>/imsU',
	);
	$repl = array(
		'',
		' <',
		'> ',
	);

	return preg_replace($patt, $repl, $data);
} // spider_process_search_data

function spider_add_word($word)
{
	global $db;

	$sql = "SELECT * FROM spider_words WHERE sw_word='$word'";
	if($item = $db->ExecuteSingle($sql))
	{
		return $item['sw_id'];
	}

	$sql = "INSERT INTO spider_words (sw_word) VALUES ('$word')";
	if($db->Execute($sql))
	{
		return $db->LastID();
	}
} // spider_add_word

function spider_get_hash($url)
{
	global $db;

	$sql = "SELECT * FROM spider_urls WHERE su_url='$url'";
	if($item = $db->ExecuteSingle($sql))
	{
		return $item['su_hash'];
	}

	return false;
} // spider_get_hash

function spider_update_url($url_data)
{
	global $db;

	$sql = "
UPDATE spider_urls SET
	su_hash = '$url_data[hash]',
	su_name = '$url_data[name]',
	su_date = '$url_data[date]',
	su_module_id = '$url_data[module_id]',
	su_module_name = '$url_data[module_name]',
	su_bad_user = $url_data[bad_user]
WHERE
	su_url = '$url_data[url]'";

	return $db->Execute($sql);
} // spider_update_url

function spider_insert_url($url_data)
{
	global $db;

	$sql = sprintf("
INSERT INTO spider_urls (
	su_url, su_hash, su_name, su_date,
	su_module_id, su_module_name, su_bad_user
) VALUES (
	'%s', '%s', '%s', '%s',
	'%s', '%s', %d
)",
	$url_data['url'], $url_data['hash'], addslashes($url_data['name']), $url_data['date'],
	$url_data['module_id'], addslashes($url_data['module_name']), $url_data['bad_user']
);

	return $db->Execute($sql);
} // spider_insert_url


function spider_fetch_url($url)
{
	global $db;

	$sql = "SELECT * FROM spider_urls WHERE su_url='$url'";

	return $db->ExecuteSingle($sql);
} // spider_fetch_url

/**
	retruns: su_id (spider_urls ID)
*/
function spider_add_url($url_data)
{
	global $db;

	if($item = spider_fetch_url($url_data['url']))
	{
		# update hash
		print "Update URL: $url_data[url]";
		spider_update_url($url_data);

		# remove words
		spider_cleanup_url($item['su_id']);
		print ", remove old words";

		return $item['su_id'];
	}

	print "Add URL: $url_data[url]";
	if(spider_insert_url($url_data))
	{
		return $db->LastID();
	}
} // spider_add_url

function spider_add_word2url($word_id, $url_id)
{
	global $db;

	$sql1 = "INSERT INTO spider_words2urls (sw_id, su_id, word_count) VALUES ($word_id, $url_id, 1) ON DUPLICATE KEY UPDATE word_count = word_count + 1";
	//$sql2 = "UPDATE spider_words SET sw_urls = sw_urls + 1 WHERE sw_id = $word_id)";

	return $db->Execute($sql1) /*&& $db->Execute($sql2)*/;
} // spider_add_word2url

# Izdzēš URL`a vārdus
function spider_cleanup_url($su_id)
{
	global $db;

	$sql = "DELETE FROM spider_words2urls WHERE su_id = $su_id";

	return $db->Execute($sql);
} // spider_cleanup_url

function spider_update_url_mass($indexes)
{
	global $db;

	$sql = "SELECT * FROM spider_urls";
	$q = $db->Query($sql);
	while($item = $db->FetchAssoc($q))
	{
		$url_data = false;
		foreach($indexes as $o)
		{
			if($url_data = $o->navigate($item['su_url']))
			{
				break;
			}
		}

		if(!$url_data)
		{
			print "Cannot navigate: $item[su_url] - do nothing\n";
			continue;
		}

		if($item['su_hash'] != $url_data['hash'])
		{
			print "Need re-index: $item[su_url] - do nothing\n";
			continue;
		}

		print "Update: $item[su_url]\n";
		spider_update_url($url_data);
	}
} // spider_update_url_mass

function spider_crawl($indexes)
{
	global $SPIDER_WORD_LEN_MAX;

	$latest = spider_latest();
	foreach($indexes as $o)
	{
		$o->queryNew($latest);
		while($item = $o->fetch())
		{
			# nekas jauns
			if(($hash = spider_get_hash($item['url'])) && ($hash == $item['hash']))
			{
				print "Skip URL: $item[url]\n";
				continue;
			}

			$url_id = spider_add_url($item);
			$words = mb_split('\W', $item['data']);
			$word_count = 0;
			foreach($words as $w)
			{
				if($w)
				{
					$word_count++;
					$w = mb_substr($w, 0, $SPIDER_WORD_LEN_MAX);
					$word_id = spider_add_word($w);
					spider_add_word2url($word_id, $url_id);
				}
			}
			print ", words:$word_count\n";
		}
	}
} // spider_crawl

function spider_latest()
{
	global $db;

	$latest = array();

	$sql = "SELECT su_module_id, MAX(su_date) su_date FROM spider_urls GROUP BY su_module_id";
	if($data = $db->Execute($sql))
	{
		foreach($data as $item)
		{
			$latest[$item['su_module_id']] = $item['su_date'];
		}
	}

	return $latest;
} // spider_latest

/*
$clean = array(
	'spider_urls',
	'spider_words',
	'spider_words2urls',
);

foreach($clean as $table)
{
	$db->Execute("TRUNCATE TABLE $table");
}
*/
$forum = new SpiderForum(array('db'=>$db, 'sys_http_root'=>$sys_http_root));
$article = new SpiderArticle(array('db'=>$db, 'sys_http_root'=>$sys_http_root, 'sys_lang'=>$sys_lang));
$article_comment = new SpiderArticleComment(array('db'=>$db, 'sys_http_root'=>$sys_http_root, 'sys_lang'=>$sys_lang));

$indexes = array(
	$forum,
	$article,
	$article_comment,
);

/*
$latest = spider_latest();
$o = $article;
$o->queryNew($latest);
while($item = $o->fetch())
{
	printr($item);
}
*/

//printr($a);
//spider_update_url_mass($indexes);
print sprintf("Begin: %s\n", date('Y-m-d H:i:s'));

spider_crawl($indexes);

print sprintf("%s Done.\n", date('Y-m-d H:i:s'));

/*
$item = $article->navigate('/article/425/');
printr($item);
*/

/*
$item = $article_comment->navigate('/article/455/#comment17397');
printr($item);

$item = $article->navigate('/article/425/');
printr($item);

$item = $forum->navigate('/forum/81344/#comment82114');
printr($item);
*/

//spider_crawl($indexes);

