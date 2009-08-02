<?php
// Hackers.lv Web Engine v2.0
//
// contacts:
// http://www.hackers.lv/
// mailto:marrtins@hackers.lv

//

require_once('lib/CommentConnect.php');

if(empty($data['c_data']))
{
	$template->enable('BLOCK_comment_error');
	$template->set_var('error_msg', 'Nekorekti aizpildīta forma!', 'BLOCK_comment_error');
	return;
}

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

