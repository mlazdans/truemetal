<?php

# Comment actions
if(in_array($action, array('comment_delete', 'comment_show', 'comment_hide')))
{
	if(include("module/admin/comment/action.inc.php"))
	{
		header("Location: ".($l_id ? "$module_root/$l_id/" : "$module_root"));
	}
	return;
}

require_once('lib/CommentConnect.php');

$template = new AdminModule($sys_template_root.'/admin', "logins/edit");
$template->set_title('Admin :: logini :: rediģēt');

$Logins = new Logins();
$login = $Logins->load(array(
	'l_id'=>$l_id,
	'l_active'=>LOGIN_ALL,
	'l_accepted'=>LOGIN_ALL,
	));

//$template->enable('BLOCK_login_edit');
$template->set_array($login);

$YN = array(
	'l_active',
	'l_accepted',
	'l_emailvisible',
	'l_logedin',
	);

foreach($YN as $k)
{
	$v = sprintf("%s_%s_sel", $k, $login[$k]);
	$template->set_var($v, ' selected="selected"');
}

# User comments
$template->set_file('FILE_comment_list', 'comment/list.tpl');
$template->copy_block('BLOCK_login_comments', 'FILE_comment_list');

$CC = new CommentConnect();
$CC->setDb($db);
$comments = $CC->get(array(
	'c_userid'=>$l_id,
	'c_visible'=>COMMENT_ALL,
	'sort'=>'c_entered DESC',
	));

include("module/admin/comment/list.inc.php");

$template->out();

