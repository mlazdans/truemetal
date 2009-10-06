<?php
// dqdp.net Web Engine v3.0
//
// contacts:
// http://dqdp.net/
// marrtins@dqdp.net

//

require_once('lib/CommentConnect.php');

if(empty($data['c_data']))
{
	$template->enable('BLOCK_comment_error');
	$template->set_var('error_msg', 'Nekorekti aizpildīta forma!', 'BLOCK_comment_error');
	return;
}

# Nočeko vai iepostēts tikai links
$c_data = $data['c_data'];
$url_pattern = url_pattern();
if(preg_match_all($url_pattern, $c_data, $matches))
{
	foreach($matches[0] as $k=>$v)
		$c_data = str_replace($matches[0][$k], '', $c_data);
}

$c_data = trim($c_data);

if(empty($c_data))
{
	$template->enable('BLOCK_comment_error');
	$template->set_var('error_msg', 'Pārāk pliks tas komentārs - links bez teksta!', 'BLOCK_comment_error');

	$template->set_var('c_data', $data['c_data'], 'BLOCK_addcomment');
	return;
}
#

$cData = array(
	'c_userid'=>$_SESSION['login']['l_id'],
	'c_userlogin'=>$_SESSION['login']['l_login'],
	'c_username'=>$_SESSION['login']['l_nick'],
	'c_useremail'=>$_SESSION['login']['l_email'],
	'c_data'=>$data['c_data'],
	'c_userip'=>$ip,
	);

$CommentConnect = new CommentConnect($table);
$CommentConnect->setDb($db);

return $CommentConnect->add($table_id, $cData);

