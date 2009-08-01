<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

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

require_once('../classes/sphinxapi.php');
require_once('../classes/class.MainModule.php');
require_once('../classes/class.Article.php');

$special_search_q = urlencode($search_q);
$ent_search_q = ent($search_q);

$search_msg = array();

$template = new MainModule($sys_template_root, $sys_module_id, 'tmpl.index.php');
$template->set_file('FILE_search', 'tmpl.search.php');
$template->set_title("Meklēšana: $ent_search_q");
$template->copy_block('BLOCK_middle', 'FILE_search');

$template->set_var('search_q', $ent_search_q);

if(mb_strlen($search_q) < 3)
{
	$search_msg[] = "Vismaz 3 simbolus!";
} else {
	# Log
	if($search_log)
	{
		$sql = sprintf("
INSERT INTO `search_log` (
	`sl_userid`, `sl_q`, `sl_ip`, `sl_entered`
) VALUES (
	%s, '%s', '%s', NOW()
);",
			isset($_SESSION['login']['l_id']) ? $_SESSION['login']['l_id'] : "NULL",
			addslashes($search_q),
			$_SERVER['REMOTE_ADDR']
			);
		$db->Execute($sql);
	}

	# Sphinx
	$cl = new SphinxClient();
	$cl->SetConnectTimeout(2);
	if(($res = $cl->Query($search_q, "test1")) === false)
	{
		$search_msg[] = "Kļūda: ".$cl->GetLastError();
	} else {
		//if ( $cl->GetLastWarning() )
		//	print "WARNING: " . $cl->GetLastWarning() . "\n\n";
		//printr($res);
		if(isset($res["matches"]) && is_array($res["matches"]))
		{
			$art_ids = array_keys($res["matches"]);
			$Article = new Article();
			$items = $Article->load(array(
				'art_ids'=>$art_ids,
				));
		} else {
			$search_msg[] = "Nekas netika atrasts";
		}
	}
}


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

# Articles
if(isset($items) && $items)
{
	$template->enable('BLOCK_search');
	foreach($items as $item)
	{
		$template->set_var('su_url', "/$item[module_id]/$item[art_id]/?hl=$special_search_q", 'BLOCK_searchitem');
		$template->set_var('su_name', $item['art_name'], 'BLOCK_searchitem');
		//$template->set_var('su_date', date('m.d.Y', strtotime($item['art_entered'])), 'BLOCK_searchitem');
		$template->set_var('su_module_name', $item['module_name'], 'BLOCK_searchitem');
		$template->parse_block('BLOCK_searchitem', TMPL_APPEND);
	}
}


$path = array('archive'=>array('module_id'=>'search', 'module_name'=>'MEKLĒT'));

$template->set_right();
$template->set_search($ent_search_q);
$template->set_reviews();
$template->set_poll();
$template->set_online();

$template->out();


