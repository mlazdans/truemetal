<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//$sql_cache = 'SQL_NO_CACHE';
$sql_cache = '';

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$search_log = true;
	$search_q = $_POST['search_q'];
	if(!empty($_POST['spam']))
	{
		$search_log = false;
	}
	if(get_magic_quotes_gpc())
		$search_q = stripslashes($search_q);
} else {
	$search_log = false;
	$search_q = urldecode(get('search_q'));
}

require_once('../classes/class.MainModule.php');

$special_search_q = urlencode($search_q);
$ent_search_q = ent($search_q);

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_file('FILE_search', 'tmpl.search_new.php');
$template->set_title($sys_lang_def['search'].": $ent_search_q");
$template->set_array($sys_lang_def, 'BLOCK_middle');
$template->copy_block('BLOCK_middle', 'FILE_search');

$template->set_var('search_q', $ent_search_q);

$search_msg = array();

if(mb_strlen($search_q) < 3)
{
	$search_msg[] = $sys_lang_def['search_error'];
} else {
	/*
	$f = fopen($sys_root.'/../utils/search.log', 'a');
	$login_id = isset($_SESSION['login']['l_id']) ? $_SESSION['login']['l_id'] : 0;
	fputs($f, '['.date('Y-m-d H:i:s')."] login_id=$login_id\t$_SERVER[REMOTE_ADDR]\t$search_q\n");
	fclose($f);
	*/
	if($search_log)
	{
		$sql = sprintf(
			"INSERT INTO search_log (sl_userid, sl_q, sl_ip, sl_entered) VALUES (%s, '%s', '%s', NOW());",
			isset($_SESSION['login']['l_id']) ? $_SESSION['login']['l_id'] : "NULL",
			addslashes($search_q),
			$_SERVER['REMOTE_ADDR']
			);
		$db->Execute($sql);
	}

	$data = false;
	$q = parse_search_q($search_q);
	$spider_params = search_to_spider($q, array('sw.sw_word'));

	if($spider_params)
	{
		# NOTE: fīča variantam nr3 sameklēt vārdus pēc URL skaita,
		# sasortēt pēc mazākā (jo mazāk urļi, jo mazāk jāmeklē pēc cita vārda)
		$sql_for_words = "
SELECT
$sql_cache
	*
FROM
	spider_words sw
WHERE
	$spider_params[match]
ORDER BY
	sw_urls ASC
";
		if($GLOBALS['i_am_admin'])
		{
			//printr($sql_for_words);
		}
		$words_data = $db->Execute($sql_for_words);
		// TODO: ko darīt ar, piemēram, `un ūņ`
		// TODO: piedāvāt meklēšanas variantus pēc dažiem vārdiem (varbūt arī variantus?)
		/*
		$search_words_msg = '';
		foreach($words_data as $wd)
		{
			$search_words_msg .= $wd['sw_word'].' ';
		}
		$template->set_var('search_words_msg', $search_words_msg);
		*/

		if(count($words_data) < count($spider_params['words']))
		{
			/*
			$search_suggest_msg = $sys_lang_def['search_suggest'];
			foreach($words_data as $wd)
			{
				$search_suggest_msg .=
			}
			$search_msg[] = ;
			*/
		} else {
			# paņemam ne-populārāko vārdu, dabūjam visus vārdus
			$humble = array_shift($words_data);
			$humble_urls = array();

			$sql = "SELECT $sql_cache su_id FROM spider_words2urls WHERE sw_id = $humble[sw_id]";
			$q = $db->Query($sql);
			$c = 0;
			while($r = $db->FetchAssoc($q))
			{
				$humble_urls[$r['su_id']] = $r;
			}

			foreach($words_data as $wd)
			{
				foreach($humble_urls as $k=>$item)
				{
					# ja vārds nav atrodams
					if(!$db->ExecuteSingle("SELECT $sql_cache su_id FROM spider_words2urls WHERE sw_id = $wd[sw_id] AND su_id = $item[su_id]"))
					{
						unset($humble_urls[$k]);
					}
				}
			}

			// TODO: izdomāt, ko darīt ar lieliem settiem, piemēram `un arī`
			if(count($humble_urls) > 200)
			{
				$search_msg[] = 'Pārāk daudz ierakstu - rādam tikai 200';
				$humble_urls = array_slice($humble_urls, 0, 200);
				//break;
			}

			$sql_add = '';
			if(!empty($_SESSION['login']['l_disable_bobi']))
			{
				$sql_add .= " AND su.su_bad_user = 0";
			}

			$data = array();
			foreach($humble_urls as $item)
			{
				$sql = "
SELECT
	su.*,
	DATE_FORMAT(su.su_date, '%Y:%m:%d:%H:%i') su_date_proc,
	UNIX_TIMESTAMP(su.su_date) su_date_ts
FROM
	spider_urls su
WHERE
	su_id = $item[su_id]
	$sql_add
				";
				if($url = $db->ExecuteSingle($sql))
				{
					$data[] = $url;
				}
			}
/*
			$q = $db->Query($sql);
			while($r = $db->FetchAssoc($q))
			{
			$sql = "
SELECT
	su.*,
	DATE_FORMAT(su.su_date, '%Y:%m:%d:%H:%i') su_date_proc,
	UNIX_TIMESTAMP(su.su_date) su_date_ts
FROM
	spider_words2urls w2u
JOIN spider_urls su ON su.su_id = w2u.su_id
WHERE
	w2u.sw_id = $popular[sw_id]
			";
				$url_data[$r['su_id']][] = $r;
			}
			$url_data = array();
			printr($url_data);
*/
// NOTE: variants nr2 - darbojas lēni
/*
			$sw_id_list = '';
			foreach($words_data as $wd)
			{
				$sw_id_list .= "$wd[sw_id],";
			}
			$sw_id_list = substr($sw_id_list, 0, -1); # noņem komantu

			$sql = sprintf("
SELECT
SQL_NO_CACHE
	su.su_id,
	COUNT(*) sk,
	SUM(w2u.word_count) word_count
FROM
	spider_words2urls w2u
JOIN spider_urls su ON su.su_id = w2u.su_id
WHERE
	w2u.sw_id IN (%s)
GROUP BY
	su.su_id
HAVING
	sk = %d
", $sw_id_list, count($words_data));

			$url_data = $db->Execute($sql);
			foreach($url_data as $item)
			{
				$sql = "
SELECT
	su.*,
	DATE_FORMAT(su.su_date, '%Y:%m:%d:%H:%i') su_date_proc,
	UNIX_TIMESTAMP(su.su_date) su_date_ts
FROM
	spider_urls su
WHERE
	su_id = $item[su_id]
				";
				$data[] = $db->ExecuteSingle($sql);
			}
*/

// NOTE: variants nr1 - darbojas tikai ar vienu vārdu
/*
		$sql = "
SELECT
SQL_NO_CACHE
	sw.sw_word,
	w2u.word_count,
	su.su_url,
	su.su_name,
	su.su_date,
	DATE_FORMAT(su.su_date, '%Y:%m:%d:%H:%i') su_date_proc,
	UNIX_TIMESTAMP(su.su_date) su_date_ts
FROM
	spider_words sw
JOIN spider_words2urls w2u ON w2u.sw_id = sw.sw_id
JOIN spider_urls su ON su.su_id = w2u.su_id
";
# FIXME: performance ORDER BY
//ORDER BY
//	su.su_date DESC
		//printr($sql);
*/

		} // count(words)
	} // $spider_params

	if($data)
	{
		$sort = array();
		foreach($data as $k=>$v)
		{
			$sort[$k] = $v['su_date_ts'];
		}
		array_multisort($sort, SORT_NUMERIC, SORT_DESC, $data);

		$template->enable('BLOCK_search');
		foreach($data as $item)
		{
			$parts = preg_split('/#/', $item['su_url']);
			if(count($parts) > 1)
			{
				$url = array_shift($parts);
				$a = array_shift($parts);
				$item['su_url'] = $url."?hl=$special_search_q#$a".join('#', $parts);
			} else {
				$item['su_url'] .= "?hl=$special_search_q";
			}
			//$item['su_date_f'] = proc_date($item['su_date_proc']);
			//$item['su_name'] .= ':'.$item['sw_word'];

			$template->set_array($item, 'BLOCK_searchitem');
			$template->parse_block('BLOCK_searchitem', TMPL_APPEND);
		}
	} else {
		$search_msg[] = $sys_lang_def['search_not_found'];
	}
}
//$template->enable('BLOCK_cat_name');
//$template->set_var('current_module_name', tolower($sys_lang_def['search'], true));

if($search_msg)
{
	$block = 'BLOCK_search_msg';
	$template->enable($block);
	foreach($search_msg as $msg)
	{
		$template->set_var('search_msg', $msg, $block);
		$template->parse_block($block, TMPL_APPEND);
	}
}

$path = array('archive'=>array('module_id'=>'search', 'module_name'=>'MEKLĒT'));

$template->set_right();
$template->set_search($ent_search_q);
$template->set_reviews();
$template->set_poll();
$template->set_online();
$template->set_calendar();

$template->out();

